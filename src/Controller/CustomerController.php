<?php
    namespace Drupal\customer\Controller;

    use Drupal\Core\Controller\ControllerBase;

    /**
     * Define the class for controller.
     */
    class CustomerController extends ControllerBase {
        function index() {
            return array(
                '#type' => 'markup',
                '#markup' => $this->t('Customer module'),
            );
        }
    }
?>