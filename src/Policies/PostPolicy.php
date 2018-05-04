<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 01/05/2018
 * Time: 16:08
 */

namespace App\Policies;


class PostPolicy
{
    /**
     * On update, check if user have writte Post
     * @param $user
     * @param $post
     * @return bool
     */
    public function update($user, $post){
        return $user->id==$post->user_id;
    }

}