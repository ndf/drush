<?php

namespace Drush\Commands\core;

use Consolidation\AnnotatedCommand\CommandData;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\field\EntityDisplayRebuilder;
use Drush\Commands\DrushCommands;
use Drush\Exceptions\UserAbortException;

class DisplayCommands extends DrushCommands {

    /**
     * Rebuild display configuration for all entities and bundles.
     *
     * @command entity:rebuild-display-config
     * @bootstrap full
     * @kernel update
     * @usage drush entity:rebuild-display-config
     *
     */
    public function entityUpdates($options = []) {
        if ($this->getConfig()->simulate()) {
            throw new \Exception(dt('rebuild-display-config command does not support --simulate option.'));
        }

        if (!$this->getConfig()->simulate() && !$this->io()->confirm(dt('Do you really want to continue?'))) {
            throw new UserAbortException();
        }

        /* @var \Drupal\field\EntityDisplayRebuilder $entity_display_rebuilder */
        $entity_display_rebuilder = \Drupal::classResolver(EntityDisplayRebuilder::class);
        $bundle_info = \Drupal::service('entity_type.bundle.info')->getAllBundleInfo();
        foreach ($bundle_info as $entity_type => $bundles) {
            foreach ($bundles as $bundle => $bundle_settings) {
                $entity_display_rebuilder->rebuildEntityTypeDisplays($entity_type, $bundle);
                $this->logger()->notice('Rebuild displays of: ' . $entity_type . ':' . $bundle);
            }
        }

        $this->logger()->success(dt('Finished rebuild display config. Do a config export to see the results.'));
    }

}
