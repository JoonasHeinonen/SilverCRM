<?php
    /**
     * @file
     * Contains \Drupal\project_manager\Form\ProjectWorkingHours
     */
    namespace Drupal\project_manager\Form;

    use Drupal\Core\Database\Database;
    use Drupal\Core\Form\FormBase;
    use Drupal\Core\Form\FormInterface;
    use Drupal\Core\Form\FormStateInterface;

    class ProjectWorkingHours extends FormBase {

        /**
         * (@inheritdoc)
         */
        public function getFormId() {
            return 'project_manager_working_hours_form';
        }

         /**
         * (@inheritdoc)
         */
        public function buildForm(array $form, FormStateInterface $form_state) {
            $node = \Drupal::routeMatch()->getParameter('node');
            $nid  = $node->nid->value;
            
            $form = array();

            $form['working_hours']['project'] = array(
                '#type' => 'select',
                '#title' => t('Worked on the project.'),
                '#required' => TRUE,
                '#options' => array_combine($this->get_projects(), $this->get_projects()),
                '#default_value' => 0,
            );

            $form['working_hours']['time'] = array(
                '#type' => 'fieldset',
                '#title' => t('Time and date for work starting and ending.'),
                '#prefix' => '<div class="myclass">',
                '#attributes' => array('class' => array('container-inline')), 
                '#required' => TRUE,
            );

            $form['working_hours']['time']['start_time'] = array(
                '#type' => 'datetime',
                '#required' => TRUE,
            );

            $form['working_hours']['time']['end_time'] = array(
                '#type' => 'datetime',
                '#suffix' => '</div>',
                '#required' => TRUE,
            );

            $form['working_hours']['description'] = array(
                '#type' => 'textarea',
                '#title' => t('What was worked on the given project. <b>(Max length 200 characters!)</b>'),
                '#required' => TRUE,
                '#maxlength' => 200,
                '#rows' => 2,
                '#resizable' => 'none',
            );

            $form['working_hours']['submit'] = array(
                '#type' => 'submit',
                '#value' => t('Submit'),
            );


            $form['nid'] = array(
                '#type' => 'hidden',
                '#value' => $nid
            );

            return $form;
        }

        /**
         * {@inheritdoc}
         */
        public function validateForm(array &$form, FormStateInterface $form_state) {
            if ($form_state->getValue('end_time') <= $form_state->getValue('start_time')) {
                $form_state->setErrorByName('end_time', $this->t($this->working_hours_error('End time can not be older than start time.')));
            }

            if ($form_state->getValue('project') == 'No project available...') {
                $form_state->setErrorByName('project', $this->t($this->working_hours_error('No projects available currently.')));
            }
        }

        /**
         * (@inheritdoc)
         */
        public function submitForm(array &$form, FormStateInterface $form_state) {
            // Empty, initialize when validation works.
        }

        // Custom functions
        
        public function get_projects() {
            $query = db_select('node_field_data', 'nfd');

            $query
                ->fields('nfd', array('title'))
                ->condition('type', 'project')
                ->range(0, 50)
                ->orderBy('nfd.title', 'ASC');

            $results = $query->execute();

            foreach($results as $result) {
                $rows[] = $result->title;
            }

            if ($rows == null) {
                $rows[] = 'No project available...';
            }

            return $rows;
        }

        public function working_hours_error($msg) {
            $error_msg = 'Working hours unable to save: <b><em>' . $msg . '</b></em>';
            return $msg;
        }
    }
?>