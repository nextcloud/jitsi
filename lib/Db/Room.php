<?php

namespace OCA\jitsi\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

/**
 * @method string getName()
 * @method void setName(string $name)
 * @method string getCreatorId()
 * @method void setCreatorId(string $creatorId)
 * @method string getPublicId()
 * @method void setPublicId(string $publicId)
 */
class Room extends Entity implements JsonSerializable
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $creatorId;

    /**
     * @var string
     */
    protected $publicId;

    public function __construct()
    {
        $this->addType('id', 'integer');
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'publicId' => $this->getPublicId(),
        ];
    }
}
