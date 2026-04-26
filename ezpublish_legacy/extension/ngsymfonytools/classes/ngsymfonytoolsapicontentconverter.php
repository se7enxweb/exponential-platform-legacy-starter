<?php

// Ibexa DXP 5.0 moved the Repository interface to the Ibexa namespace.
// Accept either the legacy eZ alias or the new Ibexa interface so the class
// works regardless of which namespace is resolved at runtime.
if ( interface_exists( 'Ibexa\Contracts\Core\Repository\Repository', true )
    && !interface_exists( 'eZ\Publish\API\Repository\Repository', true ) )
{
    class_alias( 'Ibexa\Contracts\Core\Repository\Repository', 'eZ\Publish\API\Repository\Repository' );
}

use \eZ\Publish\API\Repository\Repository;

class NgSymfonyToolsApiContentConverter
{
    /**
     * @var NgSymfonyToolsApiContentConverter
     */
    private static $instance;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    /**
     * Instantiates the class object
     *
     * @return NgSymfonyToolsApiContentConverter
     */
    public static function instance()
    {
        if ( self::$instance === null )
        {
            $serviceContainer = ezpKernel::instance()->getServiceContainer();
            self::$instance = new self( $serviceContainer->get( 'ezpublish.api.repository' ) );
        }

        return self::$instance;
    }

    /**
     * Constructor
     *
     * @private
     *
     * @param \eZ\Publish\API\Repository\Repository $repository
     */
    private function __construct( Repository $repository )
    {
        $this->repository = $repository;
    }

    /**
     * Disallows cloning
     */
    private function __clone()
    {
    }

    /**
     * Converts eZ Publish legacy objects and nodes to content and locations
     *
     * @param mixed $object
     *
     * @return mixed
     */
    public function convert( $object )
    {
        if ( $object instanceof eZContentObject )
        {
            return $this->repository->getContentService()->loadContent( $object->attribute( 'id' ) );
        }
        else if ( $object instanceof eZContentObjectTreeNode )
        {
            return $this->repository->getLocationService()->loadLocation( $object->attribute( 'node_id' ) );
        }

        return $object;
    }
}
