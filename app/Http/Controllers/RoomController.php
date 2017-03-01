<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoomController extends Controller
{
    private function getRouteName()
    {
        return 'room';
    }

    public function delete($id)
    {
        Room::destroy($id);

        $data = ['class' => 'alert-success', 'message' => trans('general.deleted')];

        return response()->json($data);
    }

    public function index()
    {
        $title = trans('general.rooms');

        $dataset = Room::select('id', 'number', 'floor', 'capacity', 'price')
            ->paginate($this->getItemsPerPage());

        $view_data = [
            'columns'    => $this->getColumns(),
            'dataset'    => $dataset,
            'route_name' => $this->getRouteName(),
            'title'      => $title,
        ];

        return view('list', $view_data);
    }

    public function showAddEditForm($id = null)
    {
        if ($id === null) {
            $dataset = new Room();
            $title = trans('general.add');
            $submit_route = route($this->getRouteName().'.postadd');
        } else {
            try {
                $dataset = Room::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return Controller::returnBack([
                    'message'     => trans('general.object_not_found'),
                    'alert-class' => 'alert-danger'
                ]);
            }

            $title = trans('general.edit');
            $submit_route = route($this->getRouteName().'.postedit', $id);
        }

        $title .= ' '.mb_strtolower(trans('general.room'));

        $view_data = [
            'dataset'      => $dataset,
            'fields'       => $this->getFields(),
            'title'        => $title,
            'submit_route' => $submit_route,
            'route_name'   => $this->getRouteName()
        ];

        return view('addedit', $view_data);
    }

    private function getFields()
    {
        return [
            [
                'id'    => 'number',
                'title' => trans('general.number'),
                'value' => function (Room $data) {
                    return $data->number;
                },
                'type'     => 'number',
                'optional' => [
                    'required' => 'required',
                ]
            ],
            [
                'id'    => 'floor',
                'title' => trans('general.floor'),
                'value' => function (Room $data) {
                    return $data->floor;
                },
                'type'     => 'number',
                'optional' => [
                    'required' => 'required',
                ]
            ],
            [
                'id'    => 'capacity',
                'title' => trans('general.capacity'),
                'value' => function (Room $data) {
                    return $data->capacity;
                },
                'type'     => 'number',
                'optional' => [
                    'required' => 'required',
                ]
            ],
            [
                'id'    => 'price',
                'title' => trans('general.price'),
                'value' => function (Room $data) {
                    return $data->price;
                },
                'type'     => 'number',
                'optional' => [
                    'step'        => '0.01',
                    'placeholder' => '0.00',
                    'required'    => 'required',
                ]
            ],
            [
                'id'    => 'comments',
                'title' => trans('general.comment'),
                'value' => function (Room $data) {
                    return $data->comments;
                },
                'type' => 'textarea',
            ],
        ];
    }

    private function getColumns()
    {
        $dataset = [
            [
                'title' => trans('general.number'),
                'value' => function (Room $data) {
                    return $data->number;
                },
            ],
            [
                'title' => trans('general.floor'),
                'value' => function (Room $data) {
                    return $data->floor;
                },
            ],
            [
                'title' => trans('general.capacity'),
                'value' => function (Room $data) {
                    return $data->capacity;
                },
            ],
            [
                'title' => trans('general.price'),
                'value' => function (Room $data) {
                    return $data->price;
                },
            ],
        ];

        return $dataset;
    }
}
