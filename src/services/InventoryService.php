<?php
/**
 * Inventory plugin for Craft CMS
 *
 * Take stock of your field usage.
 *
 * @author    Double Secret Agency
 * @link      https://www.doublesecretagency.com/
 * @copyright Copyright (c) 2016 Double Secret Agency
 */

namespace doublesecretagency\inventory\services;

use craft\base\Field;
use DateTime;
use DateTimeZone;
use Exception;
use craft\base\Component;
use craft\db\Query;
use craft\helpers\UrlHelper;

/**
 * Class InventoryService
 * @since 2.0.0
 */
class InventoryService extends Component
{

    /**
     * Collect all layouts based on existing fields.
     *
     * @param array &$context The current template context.
     *
     * @return void
     * @throws Exception
     */
    public function getFieldLayouts(array &$context): void
    {
        // Initialize field layouts array
        $context['fieldLayouts'] = [];

        // Get field group options
        $context['fieldGroupOptions'] = $this->_getFieldGroupOptions($context['groups']);

        // Loop through fields
        foreach ($context['fields'] as $field) {

            // Get related layout and tab IDs
            $relatedIds = $this->_getRelatedLayoutIds($field);

            $layouts = [];
            foreach ($relatedIds as $row) {
                $layouts[] = $this->_getLayoutData($row);
            }

            $context['fieldLayouts'][] = [
                'field'   => $field,
                'layouts' => $layouts,
            ];
        }
    }

    /**
     * Get all options for the field group select.
     *
     * @param array $groups
     *
     * @return array
     */
    private function _getFieldGroupOptions(array $groups): array
    {
        // Initialize field group options
        $fieldGroupOptions = [
            [
                'label' => 'All Fields',
                'value' => '',
            ]
        ];

        // Loop through field groups
        foreach ($groups as $group) {
            // Append option for each group
            $fieldGroupOptions[] = [
                'label' => $group->name,
                'value' => $group->id,
            ];
        }

        // Return the field group options
        return $fieldGroupOptions;
    }

    /**
     * Get partial data for all layouts which contain the specified field.
     *
     * @param Field $field Model of specified field.
     *
     * @return array Partial data for all layouts which contain the specified field.
     */
    private function _getRelatedLayoutIds(Field $field): array
    {
        return (new Query())
            ->select(['[[layoutId]]','[[id]] AS tabId'])
            ->from(['{{%fieldlayouttabs}}'])
            ->where(['like', 'elements', "%{$field->uid}%", false])
            ->orderBy('[[layoutId]] ASC')
            ->all();
    }

    /**
     * Get relevant layout data based on field's element type.
     *
     * @param array $row Partial field layout data.
     *
     * @return array Relevant layout data to display.
     * @throws Exception
     */
    private function _getLayoutData(array $row): array
    {
        // Get element type
        $layout = (new Query())
            ->select(['[[type]],[[dateDeleted]]'])
            ->from('{{%fieldlayouts}}')
            ->where('[[id]]=:id', [':id' => $row['layoutId']])
            ->one();

        // Set default data
        $data = [
            'id'          => $row['layoutId'],
            'elementType' => null,
            'section'     => null,
            'entryType'   => null,
            'tab'         => null,
            'editLayout'  => null,
            'deleted'     => null,
        ];

        // If layout was deleted, get the timestamp
        if ($layout['dateDeleted']) {
            $tz = new DateTimeZone('UTC');
            $dt = new DateTime($layout['dateDeleted'], $tz);
            $data['deleted'] = $dt->format('U');
        }

        // If not a valid element type, bail
        if (!class_exists($layout['type'])) {
            $data['elementType'] = '<span class="error">'.$layout['type'].'</span>';
            return $data;
        }

        // Try to set element type
        try {
            // Use the element's display name
            $data['elementType'] = $layout['type']::displayName();
        } catch (Exception $e) {
            // Use the element's type
            $data['elementType'] = $layout['type'];
        }

        // Configure based on element type
        switch ($layout['type']::refHandle()) {

            case 'entry':

                // Get entry type
                $entryType = (new Query())
                    ->select(['[[id]]','[[name]]','[[sectionId]]'])
                    ->from('{{%entrytypes}}')
                    ->where('[[fieldLayoutId]]=:id', [':id' => $row['layoutId']])
                    ->one();

                // If no valid entry type, bail
                if (!$entryType) {
                    $data['section'] = 'Recently deleted';
                    return $data;
                }

                // Get section
                $sectionName = (new Query())
                    ->select(['[[name]]'])
                    ->from('{{%sections}}')
                    ->where('[[id]]=:id', [':id' => $entryType['sectionId']])
                    ->scalar();

                // Get tab
                $data['tab'] = (new Query())
                    ->select(['[[name]]'])
                    ->from('{{%fieldlayouttabs}}')
                    ->where('[[id]]=:id', [':id' => $row['tabId']])
                    ->scalar();

                // Set entry layout data
                $data['section']   = ($sectionName ?? null);
                $data['entryType'] = $entryType['name'];

                // Edit layout
                $editLayoutPath = 'settings/sections/'.$entryType['sectionId'].'/entrytypes/'.$entryType['id'];
                $data['editLayout'] = UrlHelper::cpUrl($editLayoutPath);

                break;

            case 'globalset':

                // Get global set
                $globalSet = (new Query())
                    ->select(['[[id]]','[[name]]'])
                    ->from('{{%globalsets}}')
                    ->where('[[fieldLayoutId]]=:id', [':id' => $row['layoutId']])
                    ->one();

                // If no valid global set, bail
                if (!$globalSet) {
                    $data['section'] = 'Recently deleted';
                    return $data;
                }

                // Get section
                $data['section'] = $globalSet['name'];

                // Get tab
                $data['tab'] = (new Query())
                    ->select(['[[name]]'])
                    ->from('{{%fieldlayouttabs}}')
                    ->where('[[id]]=:id', [':id' => $row['tabId']])
                    ->scalar();

                // Edit layout
                $editLayoutPath = 'settings/globals/'.$globalSet['id'].'#set-fieldlayout';
                $data['editLayout'] = UrlHelper::cpUrl($editLayoutPath);

                break;

            case 'asset':

                // Get volume
                $volume = (new Query())
                    ->select(['[[id]]','[[name]]'])
                    ->from('{{%volumes}}')
                    ->where('[[fieldLayoutId]]=:id', [':id' => $row['layoutId']])
                    ->one();

                // If no valid volume, bail
                if (!$volume) {
                    $data['section'] = 'Recently deleted';
                    return $data;
                }

                // Get section
                $data['section'] = $volume['name'];

                // Edit layout
                $editLayoutPath = 'settings/assets/volumes/'.$volume['id'].'#assetvolume-fieldlayout';
                $data['editLayout'] = UrlHelper::cpUrl($editLayoutPath);

                break;

            case 'user':

                // Get tab
                $data['tab'] = (new Query())
                    ->select(['[[name]]'])
                    ->from('{{%fieldlayouttabs}}')
                    ->where('[[id]]=:id', [':id' => $row['tabId']])
                    ->scalar();

                // Edit layout
                $editLayoutPath = 'settings/users/fields';
                $data['editLayout'] = UrlHelper::cpUrl($editLayoutPath);

                break;

            // TODO: Activate other element types

            // case 'category':
            //     $data['section']   = null;
            //     $data['entryType'] = null;
            //     $data['tab']       = null;
            //     break;

            // case 'tag':
            //     $data['section']   = null;
            //     $data['entryType'] = null;
            //     $data['tab']       = null;
            //     break;

            // case 'matrixblock':
            //     $data['section']   = null;
            //     $data['entryType'] = null;
            //     $data['tab']       = null;
            //     break;

            // case 'commerceproduct':
            //     $data['section']   = null;
            //     $data['entryType'] = null;
            //     $data['tab']       = null;
            //     break;

            // case 'commercevariant':
            //     $data['section']   = null;
            //     $data['entryType'] = null;
            //     $data['tab']       = null;
            //     break;

            // Ad Wizard
            case 'ad':

                 // Use ad group name as section
                $data['section'] = (new Query())
                     ->select(['[[name]]'])
                     ->from('{{%adwizard_groups}}')
                     ->where('[[fieldLayoutId]]=:id', [':id' => $row['layoutId']])
                     ->scalar();

                // Use ad layout name as entry type
                $data['entryType'] = (new Query())
                     ->select(['[[name]]'])
                     ->from('{{%adwizard_fieldlayouts}}')
                     ->where('[[id]]=:id', [':id' => $row['layoutId']])
                     ->scalar();

                // Get tab
                $data['tab'] = (new Query())
                     ->select(['[[name]]'])
                     ->from('{{%fieldlayouttabs}}')
                     ->where('[[id]]=:id', [':id' => $row['tabId']])
                     ->scalar();

                 // Edit layout
                 $editLayoutPath = 'ad-wizard/fieldlayouts/'.$row['layoutId'];
                 $data['editLayout'] = UrlHelper::cpUrl($editLayoutPath);

                 break;

        }

        // Return layout data
        return $data;
    }

}
