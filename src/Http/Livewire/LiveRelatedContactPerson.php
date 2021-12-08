<?php

namespace VentureDrake\LaravelCrm\Http\Livewire;

use Livewire\Component;
use Ramsey\Uuid\Uuid;
use VentureDrake\LaravelCrm\Models\Person;

class LiveRelatedContactPerson extends Component
{
    public $model;
    public $contacts;
    public $person_id;
    public $person_name;

    public function mount($model)
    {
        $this->model = $model;
        $this->getContacts();
    }

    public function link()
    {
        $data = $this->validate([
            'person_name' => 'required',
        ]);

        if ($this->person_id) {
            $person = Person::find($this->person_id);
        } else {
            $name = \VentureDrake\LaravelCrm\Http\Helpers\PersonName\firstLastFromName($data['person_name']);

            $person = Person::create([
                'external_id' => Uuid::uuid4()->toString(),
                'first_name' => $name['first_name'],
                'last_name' => $name['last_name'] ?? null,
                'user_owner_id' => auth()->user()->id,
            ]);
        }
        
        $this->model->contacts()->create([
            'external_id' => Uuid::uuid4()->toString(),
            'entityable_type' => $person->getMorphClass(),
            'entityable_id' => $person->id,
        ]);

        $this->resetFields();

        $this->getContacts();

        $this->dispatchBrowserEvent('linkedPerson');
    }

    public function updatedPersonName($value)
    {
        $this->dispatchBrowserEvent('updatedNameFieldAutocomplete');
    }

    private function getContacts()
    {
        $this->contacts = $this->model->contacts()->get();
    }

    private function resetFields()
    {
        $this->reset('person_id', 'person_name');
    }
    
    public function render()
    {
        return view('laravel-crm::livewire.related-contact-people');
    }
}