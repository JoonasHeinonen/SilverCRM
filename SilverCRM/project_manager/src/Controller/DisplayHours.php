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
                ->fields('pwh', array('pid', 'project', 'work_date', 'start_hours', 'end_hours', 'work_hours', 'description'))
                ->execute()->fetchAllAssoc('pid');
            $work_hours = 0;

            // Initialize row-element.
            $rows = array();
            foreach($result as $row => $content) {
                $rows[] = array(
                    'data' => array(
                        $content->pid,
                        $content->project, 
                        $content->work_date, 
                        $content->start_hours, 
                        $content->end_hours, 
                        $content->work_hours, 
                        $content->description, 
                    )
                );

                $work_hours = $work_hours + ($content->work_hours);
            }

            // Initialize header and output.
            $header = array('PID', 'Project', 'Date of work', 'Starting time', 'Ending time', 'Working hours', 'Description');
            $output = array();
            
            $output['total_hours']['hours'] = array(
                '#theme' => 'table',
                '#header' => $header,
                '#rows' => $rows,
            );

            $output['total_hours']['in_total'] = array(
                '#markup' => t('<b>Total working hours per year: </b><em>' . $work_hours . '</em> hrs<br/>'),
            );

            return $output;
        }
    }
?>