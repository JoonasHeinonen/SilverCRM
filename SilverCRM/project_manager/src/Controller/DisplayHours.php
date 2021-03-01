<?php
    /**
     * @file
     * Contains \Drupal\project_manager\Controller\DisplayHours
     */
    namespace Drupal\project_manager\Controller;

    use Drupal\Core\Controller\ControllerBase;

    /**
     * Class DisplayHours.
     */
    class DisplayHours extends ControllerBase{
        
        /**
         * showHours.
         * 
         * @return string
         *  Return Table format data.
         */
        public function showHours() {
            $result = \Drupal::database()->select('project_working_hours', 'pwh')
                ->fields('pwh', array('pid', 'project', 'start_time', 'end_time', 'description'))
                ->execute()->fetchAllAssoc('pid');

            // Initialize row-element.
            $rows = array();
            foreach($result as $row => $content) {
                $rows[] = array(
                    'data' => array(
                        $content->pid,
                        $content->project, 
                        $content->start_time, 
                        $content->end_time, 
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
    }
?>