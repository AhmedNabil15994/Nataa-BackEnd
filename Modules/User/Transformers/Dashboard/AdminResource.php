<?php

namespace Modules\User\Transformers\Dashboard;

use Illuminate\Http\Resources\Json\Resource;

class AdminResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'image' => $this->image ? url($this->image) : url(config('core.config.user_img_path') . '/default.png'),
            'deleted_at' => $this->deleted_at,
            'created_at' => date('d-m-Y', strtotime($this->created_at)),
        ];

        if ($this->roles) {
            $numItems = count($this->roles->toArray());
            $i = 0;
            $roleName = '';
            foreach ($this->roles as $k => $role) {
                $roleName .= $role->translate(locale())->display_name;
                if (++$i !== $numItems) { // if it is not the last index
                    $roleName .= ' - ';
                }
            }
            $data['roles'] = $roleName;
        } else {
            $data['roles'] = '';
        }
        return $data;
    }
}
