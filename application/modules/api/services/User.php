<?php

class Api_Service_User
{
    /**
     * Retourne un seul utilisateur identifié par le paramètre id.
     *
     * @param int $id
     *
     * @return array
     */
    public function get($id)
    {
        $service_user = new Service_User();
        return $service_user->find($id);
    }

    /**
     * Retourne les preferences d'un utilisateur identifié par le paramètre id.
     *
     * @param int   $id
     * @param array $preferences
     *
     * @return string
     */
    public function getPreferences($id, $preferences)
    {
        $service_user = new Service_User();
        $preferences = $service_user->savePreferences($id, $preferences);

        if (!$preferences) {
            throw new Exception('Failed saving preferences');
        }

        return $preferences->toArray();
    }
}
