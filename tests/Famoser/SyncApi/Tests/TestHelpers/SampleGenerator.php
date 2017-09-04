<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 04/12/2016
 * Time: 11:29
 */

namespace Famoser\SyncApi\Tests\TestHelpers;


use Famoser\SyncApi\Models\Communication\Entities\Base\BaseCommunicationEntity;
use Famoser\SyncApi\Models\Communication\Request\CollectionEntityRequest;
use Famoser\SyncApi\Models\Communication\Request\DeviceEntityRequest;
use Famoser\SyncApi\Models\Communication\Request\HistoryEntityRequest;
use Famoser\SyncApi\Models\Communication\Request\SyncEntityRequest;
use Famoser\SyncApi\Models\Communication\Response\DeviceEntityResponse;
use Famoser\SyncApi\Types\OnlineAction;

/**
 * Class SampleGenerator: helps to generate sample data
 *
 * @package Famoser\SyncApi\Tests
 */
class SampleGenerator
{
    const IDENTIFIER = "json_obj";

    /**
     * create a guid
     *
     * @return string
     */
    public static function createGuid()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        );
    }

    /**
     * create a guid
     *
     * @return string
     */
    public static function emptyGuid()
    {
        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0
        );
    }

    /**
     * create an entity
     *
     * @param BaseCommunicationEntity $entity
     */
    public static function createEntity(BaseCommunicationEntity $entity)
    {
        $entity->Id = static::createGuid();
        $entity->VersionId = static::createGuid();
        $entity->OnlineAction = OnlineAction::CREATE;
        $entity->Content = "{}";
        $entity->CreateDateTime = date("c");
        $entity->Identifier = SampleGenerator::IDENTIFIER;
    }

    /**
     * @return DeviceEntityRequest
     */
    public static function createDeviceEntityRequest()
    {
        $req = new DeviceEntityRequest();
        $req->Identifier = SampleGenerator::IDENTIFIER;
        return $req;
    }

    /**
     * @return HistoryEntityRequest
     */
    public static function createHistoryEntityRequest()
    {
        $req = new HistoryEntityRequest();
        $req->Identifier = SampleGenerator::IDENTIFIER;
        return $req;
    }

    /**
     * @return CollectionEntityRequest
     */
    public static function createCollectionEntityRequest()
    {
        $req = new CollectionEntityRequest();
        $req->Identifier = SampleGenerator::IDENTIFIER;
        return $req;
    }

    /**
     * @return SyncEntityRequest
     */
    public static function createSyncEntityRequest()
    {
        $req = new SyncEntityRequest();
        $req->Identifier = SampleGenerator::IDENTIFIER;
        return $req;
    }
}