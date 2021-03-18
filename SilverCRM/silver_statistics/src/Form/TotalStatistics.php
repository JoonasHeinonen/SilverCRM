<?php
    /**
     * @file
     * Contains \Drupal\project_manager\Form\DisplayHours
     */
    namespace Drupal\silver_statistics\Form;

    use Drupal\Core\Form\FormBase;
    use Drupal\Core\Form\FormInterface;
    use Drupal\Core\Form\FormStateInterface;

    /**
     * Class TotalStatistics.
     */
    class TotalStatistics extends FormBase{
        
        /**
         * (@inheritdoc)
         */
        public function getFormId() {
            return 'project_manager_export_to_csv_form';
        }

        /**
         * (@inheritdoc)
         */
        public function buildForm(array $form, FormStateInterface $form_state) {
            $form = array();

            $form['total_statistics']['header'] = array(
                '#type' => 'markup',
                '#prefix' => '<h2>Total statistics in Silver CRM',
                '#suffix' => '</h2>'
            );

            return $form;
        }

        /**
         * (@inheritdoc)
         */
        public function submitForm(array &$form, FormStateInterface $form_state) {
            drupal_set_message(t(
                'If you managed to get this, you\'re real hacker'
            ));
        }
    }
?>