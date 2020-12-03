<?php
namespace presseddigital\searchit\migrations;

use presseddigital\searchit\Searchit;

use Craft;
use craft\db\Migration;
use craft\helpers\Json;
use craft\services\Fields;
use craft\services\Plugins;

use craft\elements\Category;
use craft\elements\Entry;
use craft\elements\User;
use craft\elements\Asset;

class m190204_000001_source_to_uids extends Migration
{

    public function safeUp()
    {
        $this->_sourcesToUid();
    }

    private function _sourcesToUid()
    {
        // Get All Sources
        $elementFilters = Searchit::$plugin->getElementFilters()->getAllElementFilters();
        if($elementFilters)
        {
            foreach($elementFilters as $elementFilter)
            {
                if($elementFilter->source != Searchit::$plugin->getElementFilters()::GLOBAL_SOURCE_KEY)
                {
                    // Do we need to update the source key?
                    $uid = false;
                    $sourceKeyParts = explode(':', $elementFilter->source);
                    $sourceId = $sourceKeyParts[1] ?? false;

                    if($sourceId && ctype_digit($sourceId))
                    {
                        switch ($elementFilter->type)
                        {
                            case Category::class:
                                $uid = Craft::$app->getCategories()->getGroupById($sourceId)->uid ?? false;
                            break;

                            case Entry::class:
                                $uid = Craft::$app->getSections()->getSectionById($sourceId)->uid ?? false;
                            break;

                            case User::class:
                                $uid = Craft::$app->getUserGroups()->getGroupById($sourceId)->uid ?? false;
                            break;

                            case Asset::class:
                                $uid = Craft::$app->getAssets()->getFolderById($sourceId)->uid ?? false;
                            break;
                        }

                        // Save New Source Key
                        if($uid)
                        {
                            $elementFilter->source = $sourceKeyParts[0].':'.$uid;
                            Searchit::$plugin->getElementFilters()->saveElementFilter($elementFilter);
                        }

                    }
                }
            }
        }


    }

    public function safeDown()
    {
        echo "m190204_000001_source_to_uids cannot be reverted.\n";
        return false;
    }
}
