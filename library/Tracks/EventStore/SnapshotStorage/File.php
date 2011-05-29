<?php

namespace Tracks\EventStore\SnapshotStorage;
use Tracks\EventStore\ISnapshotStore;
use Tracks\Model\Entity, Tracks\Model\Guid;

class File implements ISnapshotStore {

   public function __construct($directory) {
      $this->directory = $directory;
   }

   public function save(Entity $entity) {
      file_put_contents($this->getFilename($entity->getGuid()), serialize($entity));
   }

   public function load(Guid $guid) {
      if (file_exists($this->getFilename($guid))) {
         return unserialize(file_get_contents($this->getFilename($guid)));
      } else {
         return NULL;
      }
   }

   private function getFilename(Guid $guid) {
      return $this->directory .'/'. (string) $guid .'.dat';
   }

   /** @var string */
   private $directory;
}