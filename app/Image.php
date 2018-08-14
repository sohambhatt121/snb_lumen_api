<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(definition="Image")
 */
class Image extends BaseModel
{
    protected $table='images';

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=20, nullable=false)
     * @SWG\Property()
     */
    public $image_id;

    /**
     *
     * @var integer
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
     * @Identity
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
    public $url;

}