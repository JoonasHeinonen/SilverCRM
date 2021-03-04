<?php
    /**
     * @file
     * Contains \Drupal\project_manager\Form\ExportToCSV
     */
    namespace Drupal\project_manager\Form;

    use Drupal\Core\Form\FormBase;
    use Drupal\Core\Form\FormInterface;
    use Drupal\Core\Form\FormStateInterface;

    class ExportToCSV extends FormBase {

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
            $node = \Drupal::routeMatch()->getParameter('node');
            $nid  = $node->nid->value;

            $form = array();

            $form['export_to_csv']['div'] = array(
                '#type' => 'fieldset',
                '#title' => t('<b>Export to CSV</b>'),
                '#required' => TRUE,
                '#prefix' => '<div class="myclass">',
                '#attributes' => array('class' => array('')), 
            );

            $form['export_to_csv']['div']['desc'] = array(
                '#markup' => t('<br/><p>This tool is used in <b>Project Manager Module</b> for CSV-export. Use this <b>feature</b> to export your working hours to a <em><b>CSV-file.</b></em></p>'),
            );

            $form['export_to_csv']['div']['download'] = array(
                '#prefix' => $this->t('<a href="working_hours.csv" download="working_hours.csv"><em><b>Click here to download the CSV-file.</b></em>'),
                '#suffix' => $this->t('</a><br/>'),
            );

            $form['export_to_csv']['div']['export'] = array(
                '#type' => 'submit',
                '#value' => $this->t('Export to CSV'),
                '#suffix' => '</div></br>',
            );
        
            return $form;
        }

        /**
         * (@inheritdoc)
         */
        public function submitForm(array &$form, FormStateInterface $form_state) {
            $csv_output = array (
                array('Project', 'Starting time', 'Ending time', 'Working hours', 'Description'),
                array('Silver CRM', '08:00', '16:00', '8', 'Lorem ipsum dolor sit amet...'),
            );
            
            $f_open = fopen('working_hours.csv', 'w');
            
            foreach ($csv_output as $fields) {
                fputcsv($f_open, $fields);
            }
            
            fclose($fp);

            drupal_set_message(t(
                'Exported working hours to <em><b>CSV-file</b></em> successfully!'
            ));
        }
    }
?>