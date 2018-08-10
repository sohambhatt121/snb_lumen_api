<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(definition="User")
 */
class User extends BaseModel
{
    protected $table='users';

    public function prospects() 
    { 
        return $this->hasMany('App\Prospects', 'customer_email', 'user_email');
    }
    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $user_id;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $user_firstname;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $user_lastname;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $user_email;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $user_contact;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $user_region;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $user_country;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $user_state;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     * @SWG\Property()
     */
    public $user_company;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     * @SWG\Property()
     */
    public $user_project;

    /**
     *
     * @var string
     * @Column(type="string", length=5000, nullable=false)
     * @SWG\Property()
     */
    public $project_details;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     * @SWG\Property()
     */
    public $time_estimation;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     * @SWG\Property()
     */
    public $budget_estimation;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     * @SWG\Property()
     */
    public $industry;

    /**
     *
     * @var number
     * @Column(type="number", length=20, nullable=false)
     * @SWG\Property()
     */
    public $subscribe;


}