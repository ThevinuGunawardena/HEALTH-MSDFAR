<?php

namespace backend\controllers;

use Yii;
use yii\db\Exception;
use backend\components\Controller;


class DatabaseUpdateController extends Controller
{
    public function actionUpdate()
    {
        // Get the database connection
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            // Check if departure_request_crew table exists and drop its foreign key constraint
            $crewTableExists = $db->createCommand("
                SELECT COUNT(*) 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'departure_request_crew'
            ")->queryScalar();

            if ($crewTableExists) {
                // Drop the foreign key constraint if it exists
                $fkExists = $db->createCommand("
                    SELECT COUNT(*) 
                    FROM information_schema.table_constraints 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'departure_request_crew' 
                    AND constraint_name = 'fk_departure_request_crew_request_id'
                ")->queryScalar();

                if ($fkExists) {
                    $db->createCommand("
                        ALTER TABLE `departure_request_crew` 
                        DROP FOREIGN KEY `fk_departure_request_crew_request_id`
                    ")->execute();
                }

                // Drop the departure_request_crew table
                $db->createCommand("
                    DROP TABLE `departure_request_crew`
                ")->execute();
            }

            // Drop departure_requests table if it exists
            $db->createCommand("
                DROP TABLE IF EXISTS `departure_requests`
            ")->execute();

            // Create the departure_requests table
            $db->createCommand("
                CREATE TABLE `departure_requests` (
                    `id` int(10) NOT NULL AUTO_INCREMENT,
                    `boat_no` varchar(20) DEFAULT NULL,
                    `boat_name` varchar(80) NOT NULL,
                    `owner` varchar(225) DEFAULT NULL,
                    `contact_no` varchar(15) DEFAULT NULL,
                    `email` varchar(225) DEFAULT NULL,
                    `skipper` varchar(225) DEFAULT NULL,
                    `skipper_no` varchar(50) DEFAULT NULL,
                    `skipper_nic` varchar(15) DEFAULT NULL,
                    `district` varchar(225) DEFAULT NULL,
                    `harbor` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
                    `fishing_area` varchar(25) DEFAULT NULL,
                    `length_longline` float DEFAULT NULL,
                    `length_gillnet` float DEFAULT NULL,
                    `length_ringnet` float DEFAULT NULL,
                    `longline_hooks` int(20) DEFAULT NULL,
                    `mesh_gillnet` float DEFAULT NULL,
                    `mesh_ringnet` float DEFAULT NULL,
                    `national_license_no` varchar(25) DEFAULT NULL,
                    `hs_license_no` varchar(25) DEFAULT NULL,
                    `vms` varchar(10) DEFAULT NULL,
                    `agree` varchar(10) DEFAULT NULL,
                    `req_date_time` datetime(6) DEFAULT NULL,
                    `user` varchar(225) DEFAULT NULL,
                    `action_date` datetime(6) DEFAULT NULL,
                    `approve` varchar(10) DEFAULT NULL,
                    `remarks` varchar(225) CHARACTER SET utf8 DEFAULT NULL,
                    `water_bot` varchar(15) DEFAULT NULL,
                    `mcs` varchar(225) DEFAULT NULL,
                    `frequency` varchar(225) DEFAULT NULL,
                    `vms_code` varchar(80) DEFAULT NULL,
                    `manual` varchar(6) DEFAULT NULL,
                    `arrivalPort` varchar(80) DEFAULT NULL,
                    `arrivalDate` varchar(80) DEFAULT NULL,
                    `arrTime` varchar(80) DEFAULT NULL,
                    `crew1` varchar(100) DEFAULT NULL,
                    `crew1_id` varchar(15) DEFAULT NULL,
                    `crew2` varchar(100) DEFAULT NULL,
                    `crew2_id` varchar(15) DEFAULT NULL,
                    `crew3` varchar(100) DEFAULT NULL,
                    `crew3_id` varchar(15) DEFAULT NULL,
                    `crew4` varchar(100) DEFAULT NULL,
                    `crew4_id` varchar(15) DEFAULT NULL,
                    `crew5` varchar(100) DEFAULT NULL,
                    `crew5_id` varchar(15) DEFAULT NULL,
                    `crew6` varchar(100) DEFAULT NULL,
                    `crew6_id` varchar(15) DEFAULT NULL,
                    `crew7` varchar(100) DEFAULT NULL,
                    `crew7_id` varchar(15) DEFAULT NULL,
                    `crew8` varchar(100) DEFAULT NULL,
                    `crew8_id` varchar(25) DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1
            ")->execute();

            // Check if request table exists and copy data
            $requestTableExists = $db->createCommand("
                SELECT COUNT(*) 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'request'
            ")->queryScalar();

            if ($requestTableExists) {
                $db->createCommand("
                    INSERT INTO `departure_requests` (
                        id, boat_no, boat_name, owner, contact_no, email, skipper, skipper_no, skipper_nic, 
                        district, harbor, fishing_area, length_longline, length_gillnet, length_ringnet, 
                        longline_hooks, mesh_gillnet, mesh_ringnet, national_license_no, hs_license_no, 
                        vms, agree, req_date_time, user, action_date, approve, remarks, water_bot, mcs, 
                        frequency, vms_code, manual, arrivalPort, arrivalDate, arrTime,
                        crew1, crew1_id, crew2, crew2_id, crew3, crew3_id, crew4, crew4_id,
                        crew5, crew5_id, crew6, crew6_id, crew7, crew7_id, crew8, crew8_id
                    )
                    SELECT 
                        id, boat_no, boat_name, owner, contact_no, email, skipper, skipper_no, skipper_nic, 
                        district, harbor, fishing_area, length_longline, length_gillnet, length_ringnet, 
                        longline_hooks, mesh_gillnet, mesh_ringnet, national_license_no, hs_license_no, 
                        vms, agree, req_date_time, user, action_date, approve, remarks, water_bot, mcs, 
                        frequency, vms_code, manual, arrivalPort, arrivalDate, arrTime,
                        crew1, crew1_id, crew2, crew2_id, crew3, crew3_id, crew4, crew4_id,
                        crew5, crew5_id, crew6, crew6_id, crew7, crew7_id, crew8, crew8_id
                    FROM `request`
                ")->execute();
            }

            // Set AUTO_INCREMENT for departure_requests
            $db->createCommand("ALTER TABLE `departure_requests` AUTO_INCREMENT = 24")->execute();

            // Create the departure_request_crew table
            $db->createCommand("
                CREATE TABLE `departure_request_crew` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `request_id` int(11) NOT NULL,
                    `nic` varchar(100) NOT NULL,
                    `name` varchar(200) NOT NULL,
                    PRIMARY KEY (`id`),
                    KEY `req_id` (`request_id`),
                    CONSTRAINT `fk_departure_request_crew_request_id` FOREIGN KEY (`request_id`) 
                        REFERENCES `departure_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1
            ")->execute();

            // Migrate crew data from departure_requests to departure_request_crew
            $db->createCommand("
                INSERT INTO `departure_request_crew` (request_id, nic, name)
                SELECT id, crew1_id, crew1 FROM `departure_requests` WHERE crew1 IS NOT NULL AND crew1_id IS NOT NULL
                UNION
                SELECT id, crew2_id, crew2 FROM `departure_requests` WHERE crew2 IS NOT NULL AND crew2_id IS NOT NULL
                UNION
                SELECT id, crew3_id, crew3 FROM `departure_requests` WHERE crew3 IS NOT NULL AND crew3_id IS NOT NULL
                UNION
                SELECT id, crew4_id, crew4 FROM `departure_requests` WHERE crew4 IS NOT NULL AND crew4_id IS NOT NULL
                UNION
                SELECT id, crew5_id, crew5 FROM `departure_requests` WHERE crew5 IS NOT NULL AND crew5_id IS NOT NULL
                UNION
                SELECT id, crew6_id, crew6 FROM `departure_requests` WHERE crew6 IS NOT NULL AND crew6_id IS NOT NULL
                UNION
                SELECT id, crew7_id, crew7 FROM `departure_requests` WHERE crew7 IS NOT NULL AND crew7_id IS NOT NULL
                UNION
                SELECT id, crew8_id, crew8 FROM `departure_requests` WHERE crew8 IS NOT NULL AND crew8_id IS NOT NULL
            ")->execute();

            // Set AUTO_INCREMENT for departure_request_crew
            $db->createCommand("ALTER TABLE `departure_request_crew` AUTO_INCREMENT = 38")->execute();

            // Drop crew-related columns from departure_requests
            $columnsToDrop = [
                'crew1', 'crew1_id', 'crew2', 'crew2_id', 'crew3', 'crew3_id', 'crew4', 'crew4_id',
                'crew5', 'crew5_id', 'crew6', 'crew6_id', 'crew7', 'crew7_id', 'crew8', 'crew8_id'
            ];
            foreach ($columnsToDrop as $column) {
                $columnExists = $db->createCommand("
                    SELECT COUNT(*) 
                    FROM information_schema.columns 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'departure_requests' 
                    AND column_name = :column
                ", [':column' => $column])->queryScalar();

                if ($columnExists) {
                    $db->createCommand("ALTER TABLE `departure_requests` DROP COLUMN `$column`")->execute();
                }
            }

            // Optionally, drop the old request table (uncomment if needed)
            // $db->createCommand("DROP TABLE `request`")->execute();

            // Commit the transaction
            $transaction->commit();
            return "Database tables dropped and recreated successfully.";
        } catch (Exception $e) {
            // Rollback on error
            $transaction->rollBack();
            return "Error during database update: " . $e->getMessage();
        }
    }

    public function actionActivityUpdate()
    {
        // Get the database connection
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            // Drop departure_boat_activity table if it exists
            $db->createCommand("
                DROP TABLE IF EXISTS `departure_boat_activity`
            ")->execute();

            // Create the departure_boat_activity table
            $db->createCommand("
                CREATE TABLE `departure_boat_activity` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `boat_no` varchar(225) NOT NULL,
                    `activity` varchar(225) NOT NULL,
                    `description` varchar(225) NOT NULL,
                    `date_time` datetime(6) NOT NULL,
                    `to_date` text NOT NULL,
                    `user_name` varchar(20) NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ")->execute();

            // Check if activity table exists and copy data
            $activityTableExists = $db->createCommand("
                SELECT COUNT(*) 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'activity'
            ")->queryScalar();

            if ($activityTableExists) {
                $db->createCommand("
                    INSERT INTO `departure_boat_activity` (
                        id, boat_no, activity, description, date_time, to_date, user_name
                    )
                    SELECT 
                        id, 
                        boat_no, 
                        activity, 
                        LEFT(description, 225), 
                        date_time, 
                        to_date, 
                        user_name
                    FROM `activity`
                ")->execute();
            }

            // Set AUTO_INCREMENT for departure_boat_activity
            $db->createCommand("ALTER TABLE `departure_boat_activity` AUTO_INCREMENT = 54669")->execute();

            // Optionally, drop the old activity table (uncomment if needed)
            // $db->createCommand("DROP TABLE `activity`")->execute();

            // Commit the transaction
            $transaction->commit();
            return "Database table departure_boat_activity created and data migrated successfully.";
        } catch (Exception $e) {
            // Rollback on error
            $transaction->rollBack();
            return "Error during database update: " . $e->getMessage();
        }
    }


    public function actionSkipperUpdate()
    {
        // Get the database connection
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            // Temporarily disable strict SQL mode to handle invalid datetimes
            $db->createCommand("SET SESSION sql_mode = 'NO_ZERO_DATE,NO_ZERO_IN_DATE'")->execute();

            // Drop departure_activity_skipper table if it exists
            $db->createCommand("
                DROP TABLE IF EXISTS `departure_activity_skipper`
            ")->execute();

            // Create the departure_activity_skipper table
            $db->createCommand("
                CREATE TABLE `departure_activity_skipper` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `skipper_id` varchar(25) NOT NULL,
                    `nic` varchar(20) NOT NULL,
                    `served_vessel` varchar(25) NOT NULL,
                    `dep_date` datetime(6) NOT NULL,
                    `dep_id` varchar(20) NOT NULL,
                    `activity` varchar(225) NOT NULL,
                    `description` varchar(225) NOT NULL,
                    `date_time` datetime(6) NOT NULL,
                    `to_date` text NOT NULL,
                    `user_name` varchar(20) NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ")->execute();

            // Check if activity_skipper table exists and copy data
            $activitySkipperTableExists = $db->createCommand("
                SELECT COUNT(*) 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'activity_skipper'
            ")->queryScalar();

            if ($activitySkipperTableExists) {
                $db->createCommand("
                    INSERT INTO `departure_activity_skipper` (
                        id, skipper_id, nic, served_vessel, dep_date, dep_id, 
                        activity, description, date_time, to_date, user_name
                    )
                    SELECT 
                        id, 
                        skipper_id, 
                        nic, 
                        served_vessel, 
                        CASE 
                            WHEN dep_date IS NULL OR 
                                 dep_date = '0000-00-00 00:00:00.000000' OR 
                                 STR_TO_DATE(dep_date, '%Y-%m-%d %H:%i:%s.%f') IS NULL 
                            THEN '1970-01-01 00:00:00.000000'
                            ELSE dep_date 
                        END AS dep_date,
                        dep_id, 
                        activity, 
                        description, 
                        CASE 
                            WHEN date_time IS NULL OR 
                                 date_time = '0000-00-00 00:00:00.000000' OR 
                                 STR_TO_DATE(date_time, '%Y-%m-%d %H:%i:%s.%f') IS NULL 
                            THEN '1970-01-01 00:00:00.000000'
                            ELSE date_time 
                        END AS date_time,
                        to_date, 
                        user_name
                    FROM `activity_skipper`
                ")->execute();
            }

            // Set AUTO_INCREMENT for departure_activity_skipper
            $db->createCommand("ALTER TABLE `departure_activity_skipper` AUTO_INCREMENT = 2238")->execute();

            // Optionally, drop the old activity_skipper table (uncomment if needed)
            // $db->createCommand("DROP TABLE `activity_skipper`")->execute();

            // Restore original SQL mode (optional, to avoid affecting other operations)
            $db->createCommand("SET SESSION sql_mode = ''")->execute();

            // Commit the transaction
            $transaction->commit();
            return "Database table departure_activity_skipper created and data migrated successfully.";
        } catch (Exception $e) {
            // Rollback on error
            $transaction->rollBack();
            return "Error during database update: " . $e->getMessage();
        }
    }


}