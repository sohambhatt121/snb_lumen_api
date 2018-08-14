<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(definition="Event")
 */
class Event extends BaseModel
{
    protected $table='events';

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=20, nullable=false)
     * @SWG\Property()
     */
    public $event_id;

    /**
     *
     * @var string
     * @Identity
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $title;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $description;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $event_date;

    /**
     *
     * @var integer
     * @Column(type="integer", length=20, nullable=false)
     * @SWG\Property()
     */
    public $month;

    /**
     *
     * @var integer
     * @Column(type="integer", length=20, nullable=false)
     * @SWG\Property()
     */
    public $year;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $video_1;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $video_2;

}