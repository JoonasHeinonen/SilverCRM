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
            $nid = null;

            if ($node instanceof \Drupal\node\NodeInterface) {
                $nid = $node->id();
            }

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
                '#title' => t('<b>Time and date for work starting and ending.</b>'),
                '#required' => TRUE,
                '#prefix' => '<div class="myclass">',
                '#attributes' => array('class' => array('container-inline')), 
            );

            $form['working_hours']['time']['work_date'] = array(
                '#type' => 'date',
                '#default_value' => date('Y-m-d'),
                '#required' => TRUE,
            );

            $form['working_hours']['time']['start_hours'] = array(
                '#type' => 'number',
                '#size' => 1,
                '#required' => TRUE,
                '#default_value' => 0,
                '#min' => 0,
                '#max' => 24,
            );

            $form['working_hours']['time']['start_minutes'] = array(
                '#type' => 'number',
                '#size' => 1,
                '#required' => TRUE,
                '#default_value' => 0,
                '#min' => 0,
                '#max' => 60,
                '#prefix' => ':',
                '#suffix' => ''
            );

            $form['working_hours']['time']['end_hours'] = array(
                '#type' => 'number',
                '#size' => 1,
                '#required' => TRUE,
                '#default_value' => 0,
                '#min' => 0,
                '#max' => 24,
                '#prefix' => ' - ',
                '#suffix' => ''
            );

            $form['working_hours']['time']['end_minutes'] = array(
                '#type' => 'number',
                '#size' => 1,
                '#required' => TRUE,
                '#default_value' => 0,
                '#min' => 0,
                '#max' => 60,
                '#prefix' => ':',
                '#suffix' => '</div></br>',
            );


            $form['working_hours']['time']['description'] = array(
                '#type' => 'textarea',
                '#title' => t('What was worked on the given project. <b>(Max length 200 characters!)</b>'),
                '#required' => TRUE,
                '#maxlength' => 400,
                '#rows' => 4,
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
            if ($form_state->getValue('end_hours') < $form_state->getValue('start_hours')) {
                $form_state->setErrorByName('end_hours', $this->t($this->working_hours_error('End time can not be older than start time.')));
            }

            if ($form_state->getValue('project') === 'No project available...') {
                $form_state->setErrorByName('project', $this->t($this->working_hours_error('No projects available currently.')));
            }
        }

        /**
         * (@inheritdoc)
         */
        public function submitForm(array &$form, FormStateInterface $form_state) {
            $user = \Drupal::currentUser();
            $user_id = $user->id();
            
            $entry = db_insert('project_working_hours')
                ->fields(array(
                    'project' => $form_state->getValue('project'),
                    'work_date' => $form_state->getValue('work_date'),
                    'start_hours' => $form_state->getValue('start_hours'),
                    'start_minutes' => $form_state->getValue('start_minutes'),
                    'end_hours' => $form_state->getValue('end_hours'),
                    'end_minutes' => $form_state->getValue('end_minutes'),
                    'work_hours' => $this->format_working_time(
                        'hour', 
                        $form_state->getValue('start_hours'),
                        $form_state->getValue('start_minutes'),
                        $form_state->getValue('end_hours'),
                        $form_state->getValue('end_minutes'),
                    ),
                    'work_minutes' => $this->format_working_time(
                        'minute', 
                        $form_state->getValue('start_hours'),
                        $form_state->getValue('start_minutes'),
                        $form_state->getValue('end_hours'),
                        $form_state->getValue('end_minutes'),
                    ),
                    'description' => $form_state->getValue('description'),
                    'uid' => $user_id,
                ))
                ->execute();

            drupal_set_message(t(
                'Working hours have been saved successfully to the database! <b>(' . $form_state->getValue('start_hours') . ':' . $form_state->getValue('start_minutes') . ' - ' . $form_state->getValue('end_hours') . ':' . $form_state->getValue('end_minutes') . ')</b>'
            ));
        }

        // Custom functions
        
        /**
         * function get_projects().
         * 
         * @return array
         *  Return Table format data.
         */
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

            if ($rows === null) {
                $rows[] = 'No project available...';
            }

            return $rows;
        }

        /**
         * function format_working_time().
         * 
         * @return integer
         *  Return Table format data.
         */
        function format_working_time($type, $start_hour, $start_minute, $end_hour, $end_minute) {
            $hours = $end_hour - $start_hour;
            $total_minutes = $start_minute + $end_minute;

            /**
             * @switch-statement.
             * 
             * Use 'hour' when the hours are to be manipulated.
             * Use 'minute' when the minutes are to be manipulated.
             */
            switch($type) {
                case 'hour':
                    if ($start_minute > 0 && $start_hour == $end_hour) {
                        $hours = $hours - 1;
                    }

                    if ($total_minutes < 60 && $hours > 0) {
                        $hours = $hours - 1;
                    }

                    if ($start_minute > 30 && $end_minute < 30) {
                        $hours = $hours - 1;
                    }

                    return $hours;
                    break;
                case 'minute':
                    if ($end_hour > $start_hour && $start_minute > 0) {
                        $total_minutes = (60 - $start_minute) + $end_minute;
                        if ($total_minutes >= 60) {
                            $total_minutes -= 60;
                        }
                        return $total_minutes;
                    } else if ($start_hour === $end_hour) {
                        $total_minutes_mere = $end_minute - $start_minute;

                        return $total_minutes_mere;
                    }

                    break;
                default:
                    break;
            }

            return 0;
        }

        /**
         * function show_hours().
         * 
         * @return string
         *  Return Table format data.
         */
        public function show_hours() {
            $result = \Drupal::database()->select('project_working_hours', 'pwh')
                ->fields('pwh', array('pid', 'project', 'start_hours', 'end_hours', 'description'))
                ->execute()->fetchAllAssoc('pid');

            // Initialize row-element.
            $rows = array();
            foreach($result as $row => $content) {
                $rows[] = array(
                    'data' => array(
                        $content->pid,
                        $content->project, 
                        $content->start_hours, 
                        $content->end_hours, 
                        $content->description, 
                    )
                );
            }

            // Initialize header.
            $header = array('PID', 'Project', 'Starting time', 'Ending time', 'Description');
            $output = array(
                '#theme' => 'table',
                '#header' => $header,
                '#rows' => $rows
            );

            return $output;
        }

        public function working_hours_error($msg) {
            $error_msg = 'Working hours unable to save: <b><em>' . $msg . '</b></em>';
            return $msg;
        }
    }
?>