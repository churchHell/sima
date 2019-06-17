<?php

namespace Tests\Support;

use App\{Group, Status};

trait GroupTrait
{

    // private $group;

    // public function groupTrait(): void
    // {
    //     $this->group = Group::first();
    // }

    public function groupToArchive(Group $group): Group
    {
        $group->status()->associate(Status::whereSlug('archived')->first())->save();
        return $group;
    }

    // public function getGroup(): Group
    // {
    //     return $this->group;
    // }
    
}