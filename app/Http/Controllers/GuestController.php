<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuestRequest;
use App\Models\Guest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Session;

class GuestController extends Controller
{
    private function getRouteName()
    {
        return 'guest';
    }

    public function index()
    {
        $title = trans('general.guests');

        $dataset = Guest::select('id', 'first_name', 'last_name', 'address', 'zip_code', 'place', 'PESEL', 'contact')
            ->paginate($this->getItemsPerPage());

        $viewData = [
            'columns'       => $this->getColumns(),
            'dataset'       => $dataset,
            'routeName'     => $this->getRouteName(),
            'title'         => $title,
            // TODO
            'deleteMessage' => mb_strtolower(trans('general.guest')).' '.mb_strtolower(trans('general.number')),
        ];

        if ($dataset->isEmpty() && !Session::has('message')) {
            Session::flash('message', trans('general.no_guests_in_database'));
            Session::flash('alert-class', 'alert-danger');
        }

        return view('list', $viewData);
    }

    public function store(GuestRequest $request, $id = null)
    {
        if ($id === null) {
            $object = new Guest();
        } else {
            try {
                $object = Guest::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return Controller::returnBack([
                    'message'     => trans('general.object_not_found'),
                    'alert-class' => 'alert-danger',
                ]);
            }
        }

        $object->fill($request->all());
        $object->save();

        return redirect()->route($this->getRouteName().'.index')
            ->with([
                'message'     => trans('general.saved'),
                'alert-class' => 'alert-success',
            ]);
    }

    public function delete($id)
    {
        Guest::destroy($id);
        $data = ['class' => 'alert-success', 'message' => trans('general.deleted')];

        return response()->json($data);
    }

    // TODO
    public function showAddEditForm($id = null)
    {
        if ($id === null) {
            $dataset = new Guest();
            $title = trans('navigation.add_guest');
            $submitRoute = route($this->getRouteName().'.postadd');
        } else {
            try {
                $dataset = Guest::select('id', 'first_name', 'last_name', 'address', 'zip_code', 'place', 'PESEL', 'contact')->findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return Controller::returnBack([
                    'message'     => trans('general.object_not_found'),
                    'alert-class' => 'alert-danger',
                ]);
            }

            $title = trans('navigation.edit_guest');
            $submitRoute = route($this->getRouteName().'.postedit', $id);
        }

        $viewData = [
            'dataset'     => $dataset,
            'fields'      => $this->getFields(),
            'title'       => $title,
            'submitRoute' => $submitRoute,
            'routeName'   => $this->getRouteName(),
        ];

        return view('addedit', $viewData);
    }

    // TODO
    private function getFields()
    {
        return [
            [
                'id'    => 'first_name',
                'title' => trans('general.first_name'),
                'value' => function (Guest $data) {
                    return $data->first_name;
                },
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'last_name',
                'title' => trans('general.last_name'),
                'value' => function (Guest $data) {
                    return $data->last_name;
                },
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'address',
                'title' => trans('general.address'),
                'value' => function (Guest $data) {
                    return $data->address;
                },
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'zip_code',
                'title' => trans('general.zip_code'),
                'value' => function (Guest $data) {
                    return $data->zip_code;
                },
                'optional' => [
                    'required'    => 'required',
                    'placeholder' => '00-000',
                ],
            ],
            [
                'id'    => 'place',
                'title' => trans('general.place'),
                'value' => function (Guest $data) {
                    return $data->place;
                },
                'optional' => [
                    'required' => 'required',
                ],
            ],
            [
                'id'    => 'PESEL',
                'title' => trans('general.PESEL'),
                'value' => function (Guest $data) {
                    return $data->PESEL;
                },
                'optional' => [
                    'required'    => 'required',
                    'placeholder' => '12345654321',
                ],
            ],
            [
                'id'    => 'contact',
                'title' => trans('general.contact'),
                'value' => function (Guest $data) {
                    return $data->contact;
                },
                'type'     => 'textarea',
                'optional' => [
                    'placeholder' => trans('general.contact_placeholder'),
                ],
            ],
        ];
    }

    private function getColumns()
    {
        $dataset = [
            [
                'title' => trans('general.first_name'),
                'value' => function (Guest $data) {
                    return $data->first_name;
                },
            ],
            [
                'title' => trans('general.last_name'),
                'value' => function (Guest $data) {
                    return $data->last_name;
                },
            ],
            [
                'title' => trans('general.address'),
                'value' => function (Guest $data) {
                    return $data->address.', '.$data->zip_code.' '.$data->place;
                },
            ],
            [
                'title' => trans('general.PESEL'),
                'value' => function (Guest $data) {
                    return $data->PESEL;
                },
            ],
            [
                'title' => trans('general.contact'),
                'value' => function (Guest $data) {
                    return $data->contact;
                },
            ],
        ];

        return $dataset;
    }
}
