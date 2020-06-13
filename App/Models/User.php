<?php

namespace App\Models;

use App\Service\Encrypt;
use Carbon\Carbon;

/**
 * User model
 */
class User extends \Core\Model
{
    /**
     * Define the database entity of the model
     *
     * @var string
     */
    protected $_entity = 'users';
    
    /**
     * Whether or not the user allowed to login in system
     *
     * @param string $userName
     * @param string $password
     * @return boolean|array
     */
    public function login($userName, $password)
    {
        // Get the password hash from database for the user
        $userData = $this->_database->get(
            $this->_entity,
            '*',
            [
                'username' => $this->_getEncUserName($userName),
                'is_enabled' => 1,
            ]
        );

        if ($userData && !password_verify($password, $userData['password'])) {
            return false;
        }

        return $userData;
    }
    
    /**
     * Find the user details by id
     *
     * @param int $id
     * @return void
     */
    public function findById($id)
    {
        return $this->_database->get(
            $this->_entity,
            '*',
            [
                'id' => $id,
                'is_enabled' => 1,
            ]
        );
    }

    /**
     * Insert the data to the database
     *
     * @param array $data
     * @return bool|int
     */
    public function insert($data = [])
    {
        $data += [
            'is_enabled' => 1,
            'created_at' => Carbon::now()->format("Y-m-d H:m:s"),
        ];

        $this->_onBeforeSave($data);

        // Save the data
        if (!$this->_database->insert($this->_entity, $data)) {
            return false;
        }
        return $this->_database->id();
    }

    /**
     * Update the user data
     *
     * @param array $data
     * @param array $args
     * @return int
     */
    public function update($data = [], $args = [])
    {
        $data += [
            'modified_at' => Carbon::now()->format("Y-m-d H:m:s"),
        ];

        $args += [
            'is_enabled' => 1,
        ];

        // Update the data
        $data = $this->_database->update($this->_entity, $data, $args);

        return $data->rowCount();
    }

    /**
     * Perform some action on data before saving it
     * 
     */
    protected function _onBeforeSave(&$data)
    {
        if ($data['username']) {
            $data['username'] = $this->_getEncUserName($data['username']);
        }

        if ($data['password']) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
    }
    
    /**
     * Returns encoded username
     *
     * @param string $userName
     * @return string
     */
    private function _getEncUserName($userName)
    {
        return (new Encrypt())->encrypt($userName);
    }

    /**
     * Whether or not the user name exist in database
     *
     * @param string $userName
     * @return boolean
     */
    public function hasUserName($userName)
    {
        $encrypt = new Encrypt();
        $encUserName = $encrypt->encrypt($userName);
        $count = $this->_database
            ->count(
                $this->_entity,
                [
                    'username' => $encUserName,
                    'is_enabled' => 1,
                ]
            );
        
        return $count > 0;
    }

    /**
     * Whether or not the user name exist in database
     *
     * @param string $userName
     * @return boolean
     */
    public function hasEmail($email)
    {
        $count = $this->_database
            ->count(
                $this->_entity,
                [
                    'email' => $email,
                    'is_enabled' => 1,
                ]
            );
        
        return $count > 0;
    }
}
