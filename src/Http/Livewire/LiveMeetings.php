<?php

namespace VentureDrake\LaravelCrm\Http\Livewire;

use Livewire\Component;
use VentureDrake\LaravelCrm\Traits\NotifyToast;

class LiveMeetings extends Component
{
    use NotifyToast;

    public $model;
    public $meetings;
    public $name;
    public $description;
    public $start_at;
    public $finish_at;
    public $showForm = false;

    protected $listeners = [
        'addMeetingActivity' => 'addMeetingOn',
        'meetingDeleted' => 'getMeetings',
        'meetingCompleted' => 'getMeetings',
     ];

    public function mount($model)
    {
        $this->model = $model;
        $this->getMeetings();

        if ($this->meetings->count() < 1) {
            $this->showForm = true;
        }
    }

    public function create()
    {
        $data = $this->validate([
            'name' => 'required',
            'description' => 'nullable',
            'start_at' => 'required',
            'finish_at' => 'required',
        ]);

        $meeting = $this->model->meetings()->create([
            'name' => $data['name'],
            'description' => $data['description'],
            'start_at' => $this->start_at,
            'finish_at' => $this->finish_at,
            'user_owner_id' => auth()->user()->id,
            'user_assigned_id' => auth()->user()->id,
        ]);

        $this->model->activities()->create([
            'causable_type' => auth()->user()->getMorphClass(),
            'causable_id' => auth()->user()->id,
            'timelineable_type' => $this->model->getMorphClass(),
            'timelineable_id' => $this->model->id,
            'recordable_type' => $meeting->getMorphClass(),
            'recordable_id' => $meeting->id,
        ]);

        $this->notify(
            'Meeting created',
        );

        $this->resetFields();
    }

    public function getMeetings()
    {
        $this->meetings = $this->model->meetings()->where('user_assigned_id', auth()->user()->id)->latest()->get();
        $this->emit('refreshActivities');
    }

    public function addMeetingToggle()
    {
        $this->showForm = ! $this->showForm;

        $this->dispatchBrowserEvent('meetingEditModeToggled');
    }

    public function addMeetingOn()
    {
        $this->showForm = true;

        $this->dispatchBrowserEvent('meetingAddOn');
    }

    private function resetFields()
    {
        $this->reset('name', 'description', 'start_at', 'finish_at');
        $this->getMeetings();
    }

    public function render()
    {
        return view('laravel-crm::livewire.meetings');
    }
}
