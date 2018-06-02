<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 02/06/2018
 * Time: 15:53
 */

namespace App\Database\entity;

/**
 * @Entity @Table(name="user")
 **/
class User
{

    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;
    /** @Column(type="string") * */
    protected $name;
    /** @ManyToMany(targetEntity="Product")* */
    protected $product;

    public function setProduct(Product $product)
    {
        $this->product = $product;
    }


}