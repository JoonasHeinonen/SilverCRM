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

            $form['total_statistics']['all_working_hours']['header'] = array(
                '#type' => 'markup',
                '#prefix' => '<h5>All working hours in the system',
                '#suffix' => '</h5>'
            );

            $form['total_statistics']['all_working_hours']['all_hours'] = array(
                '#type' => 'markup',
                '#prefix' => '<p>' . $this->getAllWorkHours(),
                '#suffix' => '</p>'
            );

            $form['total_statistics']['all_users'] = array(
                '#type' => 'markup',
                '#prefix' => '<p>' . $this->getAllUsers(),
                '#suffix' => '</p>'
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

        /**
         * Custom functions
         */

        public function getAllWorkHours() {
            $result = \Drupal::database()->select('project_working_hours', 'pwh')
                ->fields('pwh', array('pid', 'work_hours', 'work_minutes'))
                ->execute()->fetchAllAssoc('pid');

            $work_hours = 0;
            $work_minutes = 0;

            foreach($result as $row => $content) {
                $rows[] = array(
                    'data' => array(
                        $content->pid,
                        $content->work_hours, 
                        $content->work_minutes, 
                        $content->description, 
                    )
                );
                $work_hours = $work_hours + ($content->work_hours);
                $work_minutes = $work_minutes + ($content->work_minutes);

                if ($work_minutes > 60) {
                    $work_hours +=+ 1;
                    $work_minutes -= 60;
                }
            }

            return '<b>Total working hours per year: </b><em>' . $work_hours . '</em> hrs, </b><em>' . $work_minutes . '</em> minutes <br/>';
        }

        public function getAllUsers_old() {
            $result = \Drupal::database()->select('users_field_data', 'ufd')
                ->fields('ufd', array('uid'))
                ->addExpression('COUNT(uid)', 'total');

            $users = 0;

            foreach($result as $row => $content) {
                $rows[] = array(
                    'data' => array(
                        $content->uid,
                    )
                );

            }

            return '<b>All users in the system: </b><em>' . $users . '</em>';
        }

        public function getAllUsers() {
            $dataPoints = array(
                array("x"=> 10, "y"=> 41),
                array("x"=> 20, "y"=> 35, "indexLabel"=> "Lowest"),
                array("x"=> 30, "y"=> 50),
                array("x"=> 40, "y"=> 45),
                array("x"=> 50, "y"=> 52),
                array("x"=> 60, "y"=> 68),
                array("x"=> 70, "y"=> 38),
                array("x"=> 80, "y"=> 71, "indexLabel"=> "Highest"),
                array("x"=> 90, "y"=> 52),
                array("x"=> 100, "y"=> 60),
                array("x"=> 110, "y"=> 36),
                array("x"=> 120, "y"=> 49),
                array("x"=> 130, "y"=> 41)
            );
        }
    }
?>