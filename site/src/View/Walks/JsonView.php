<?php

/*
 * WALKS
 */

namespace Ramblers\Component\Ra_walks_editor\Site\View\Walks;

use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\MVC\View\JsonView as BaseJsonView;

class JsonView extends BaseJsonView {

    public function display($tpl = null) {
        try {
            $app = \JFactory::getApplication();
            $params = $app->getParams();
            $groupcode = $params->get('groupcodes');
            $groupname = $params->get('groupname');
            // Get a db connection.
            $db = \JFactory::getDbo();
            $yesterday = date("Y-m-d", strtotime("yesterday"));

            // Create a new query object.
            $query = $db->getQuery(true);
            $query->select($db->quoteName(array('id', 'state', 'date', 'category', 'content')));
            $query->from($db->quoteName('#__ra_walks_editor_walks'));
            $query->where($db->quoteName('status') . " = " . "'Published'", 'OR');
            $query->where($db->quoteName('status') . " = " . "'Cancelled'");
            $query->order('ordering ASC');

            // Reset the query using our newly populated query object.
            $db->setQuery($query);
            $results = $db->loadObjectList();
            $walks = [];

            foreach ($results as $result) {
                $datestring = \substr($result->date, 0, 10);
                if ($datestring > $yesterday) {
                    $walk = json_decode($result->content);
                    if ($walk !== null) {
                        $this->setID($walk, $result->id);
                        $walks[] = $walk;
                    } else {
                        // Error unable to decode Json walk
                    }
                }
            }

            foreach ($walks as $walk) {
                $this->checkWalk($walk);
            }
            $response = new \stdClass();
            $response->version = '1.0';
            $response->walks = $walks;

            //    $results = array_values($results); // renumber array
            echo new JsonResponse($response);
        } catch (Exception $e) {
            echo new JsonResponse($e);
        }
    }

    private function setID($walk, $id) {
        if (!property_exists($walk, 'admin')) {
            $walk->admin = new \stdClass();
        }
        if (!property_exists($walk->admin, 'id')) {
            $walk->admin->id = $id;
        }
        return $walk;
    }

    private function checkWalk($walk) {
        if (!property_exists($walk->basics, 'notes')) {
            $walk->basics->notes = '';
        }
        foreach ($walk->walks as $singleWalk) {
            if (!property_exists($singleWalk, 'gradeLocal')) {
                $singleWalk->gradeLocal = '';
            }
            if (!property_exists($singleWalk, 'pace')) {
                $singleWalk->pace = '';
            }
            if (!property_exists($singleWalk, 'ascentMetres')) {
                $singleWalk->ascentMetres = 0;
            }
            if (!property_exists($singleWalk, 'gradeLocal')) {
                $singleWalk->gradeLocal = '';
            }
        }
        if (!property_exists($walk->contact, 'email')) {
            $walk->contact->email = '';
        }
        if (!property_exists($walk->contact, 'telephone1')) {
            $walk->contact->telephone1 = '';
        }
        if (!property_exists($walk->contact, 'telephone2')) {
            $walk->contact->telephone2 = '';
        }
        if (property_exists($walk, 'notes')) {
            unset($walk->notes);
        }
        return $walk;
    }
}
