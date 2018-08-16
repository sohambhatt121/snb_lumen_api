<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(definition="Program")
 */
class Program extends BaseModel
{
    protected $table='program';

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=20, nullable=false)
     * @SWG\Property()
     */
    public $program_id;

    /**
     *
     * @var string
     * @Identity
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $program_title;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $program_description;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $program_date;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $program_location;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $program_address;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $program_time;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $contact_person;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $contact_number;

}