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

            $form['export_to_csv']['div']['file_name'] = array(
                '#prefix' => t('<p><em><ul><li>working_hours.csv'),
                '#suffix' => $this->t('</li></em></p>'),
            );

            $form['export_to_csv']['div']['download'] = array(
                '#prefix' => $this->t('<a href="working_hours.csv" download="working_hours.csv"><b><em>Click here to download the CSV-file.</em></b>'),
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
            $f_open = fopen('working_hours.csv', 'w');

            $result = \Drupal::database()->select('project_working_hours', 'pwh')
                ->fields('pwh', array('pid', 'project', 'work_date', 'start_hours', 'end_hours', 'work_hours', 'description'))
                ->execute()->fetchAllAssoc('pid');            

            // Initialize row-element.
            $rows = array();
            foreach($result as $row => $content) {
                $rows[] = array(
                    $content->pid,
                    $content->project, 
                    $content->work_date, 
                    $content->start_hours, 
                    $content->end_hours, 
                    $content->work_hours, 
                    $content->description, 
                );
            }
            
            foreach ($rows as $result) {
                fputcsv($f_open, $rows[] = array(
                    $result->pid,
                    $result->project, 
                    $result->work_date, 
                    $result->start_hours, 
                    $result->end_hours, 
                    $result->work_hours, 
                    $result->description,
                ));
            }
            
            fclose($fp);

            drupal_set_message(t(
                'Exported working hours to <em><b>CSV-file</b></em> successfully!'
            ));
        }
    }
?>