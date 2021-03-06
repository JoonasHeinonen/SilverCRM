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
            }

            // Initialize total sum of working hours in total
            $hours = 0;

            // Initialize header and output.
            $header = array('PID', 'Project', 'Date of work', 'Starting time', 'Ending time', 'Working hours', 'Description');
            $output = array();
            
            $output['total_hours']['hours'] = array(
                '#theme' => 'table',
                '#header' => $header,
                '#rows' => $rows,
            );

            $output['total_hours']['in_total'] = array(
                '#markup' => t('<em><b>Total working hours per year: </b></em>' . $hours . ' hrs<br/>'),
            );

            return $output;
        }
    }
?>