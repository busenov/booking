<?php

namespace booking\forms\AmoCRM\hipsorurzu;


use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Collections\TagsCollection;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\DateCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\SelectCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\DateCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\SelectCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\DateCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\SelectCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\Factories\NoteFactory;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\NoteModel;
use AmoCRM\Models\NoteType\CommonNote;
use AmoCRM\Models\TagModel;
use booking\entities\Order\Order;
use booking\forms\AmoCRM\LeadForms;
use function Symfony\Component\Translation\t;

class LeadPipeline7665106 extends LeadForms
{
    const PIPELINE_ID = 7665106;
    public ?LeadModel $lead;
    public string $title;
    public ?string $contact_name;                //Имя контакта
    public ?string $contact_secondName;         //Отчество контакта
    public ?string $contact_lastName;           //Фамилия контакта
    public ?string $contact_telephone;          //Телефон контакта
    public ?string $contact_email;              //Email контакта
    public ?float $budget;                      //Бюджет
    public ?string $comments = null;                    //Комментарий к сделке(сообщение формы)
    public ?string $utm_source = null;                 //utm_source
    public ?string $utm_medium = null;                 //utm_medium
    public ?string $utm_campaign = null;               //utm_campaign
    public ?string $utm_content = null;                //utm_content
    public ?string $utm_term = null;                   //utm_term
    public ?string $tags = null;                 //теги
    public ?array $notes = null;                 //примечания, массив строк
    public ?int $dateTimeSlot=null;                       //дата и время заезда
    public ?array $cars=null;                       //Тип машины
    public ?int $qty=null;                       //кол-во
    public ?Order $_order = null;


    public array $customFieldsAmoCRM = [        //соответсвтие название и коду по амоцрм
//        'utm_source'=>[273553,122825],
//        'utm_medium'=>[273555,122827],
//        'utm_campaign'=>[273557,122829],
//        'utm_term'=>[273559,122833],
//        'utm_content'=>[273561,122831],
//        'utm_referrer'=>273563,
//        'UTM'=>705095,
//        'comments'=>701479,                         //примечание
//        'sourceId'=>697153,                         //источник сделки
        'totalCount' => 791383,                       //Общее кол-во
        'typeSlot' => 791275,                         //Тип заезда
        'dateSlot' => 791221,                         //Дата заезда
        'timeSlot' => 791219,                         //Дата и Время заезда
        'urlOrder' => 792431,                         //Ссылка на заказчика
        'isPaid' => 795671,                           //Оплачено?

        'SODI_RT8_count' => 791433,                   //SODI_RT8
        'SPORT_9_count' => 791437,                    //SPORT_9

    ];


    public $leadCustomFieldsValues;

    public function __construct(
        array $config = []
    )
    {
        foreach ($config as $key => $item) {
            if (property_exists(static::class, $key)) {
                $this->$key = $item;
            }
        }

    }

    public static function CreateFromOrder(Order $order): self
    {
        $entity = new self([
            'title' => $order->getName(),
            'contact_name' => $order->customer->name,
            'contact_secondName' => $order->customer->surname,
            'contact_lastName' => $order->customer->name,
            'contact_telephone' => $order->customer->telephone,
            'contact_email' => $order->customer->email,
            'budget' => $order->total,
            '_order' => $order
        ]);
        return $entity;
    }

    public function getLead(): LeadModel
    {
        $this->lead = parent::getLead();
        $this->fillData();
        return $this->lead;
    }

    public function getContact(): ContactModel
    {
        $contact = parent::getContact();
        $name = '';
        if ($this->contact_name)
            $name = $this->contact_name;
        if ($this->contact_secondName)
            $name .= ' ' . $this->contact_secondName;
        if ($this->contact_lastName)
            $contact->setLastName($this->contact_lastName);
        if ($name)
            $contact->setName($name . ' ' . $this->contact_lastName);
        $contact->setFirstName($name);

        $customFields = new CustomFieldsValuesCollection();
        //телефон
        if ($this->contact_telephone) {
            $phoneField = (new MultitextCustomFieldValuesModel())->setFieldCode('PHONE');
            $customFields->add($phoneField);
            //Установим значение поля
            $phoneField->setValues(
                (new MultitextCustomFieldValueCollection())
                    ->add(
                        (new MultitextCustomFieldValueModel())
                            ->setEnum('WORKDD')
                            ->setValue($this->contact_telephone)
                    )
            );

        }
        //email
        if ($this->contact_email) {
            $emailField = (new MultitextCustomFieldValuesModel())->setFieldCode('EMAIL');
            $customFields->add($emailField);
            //Установим значение поля
            $emailField->setValues(
                (new MultitextCustomFieldValueCollection())
                    ->add(
                        (new MultitextCustomFieldValueModel())
                            ->setEnum('WORK')
                            ->setValue($this->contact_email)
                    )
            );
        }
        $contact->setCustomFieldsValues($customFields);
        return $contact;
    }
    public static function getContact_st(array $contact): ContactModel
    {
        $amocrm_contact = new ContactModel();

        if (array_key_exists('name',$contact))
            $amocrm_contact->setName($contact['name']);


        if (array_key_exists('firstName',$contact))
            $amocrm_contact->setFirstName($contact['firstName']);

        if (array_key_exists('lastName',$contact))
            $amocrm_contact->setFirstName($contact['lastName']);

        $customFields = new CustomFieldsValuesCollection();

        if (array_key_exists('phone',$contact)) {
            $phoneField = (new MultitextCustomFieldValuesModel())->setFieldCode('PHONE');
            $customFields->add($phoneField);
            //Установим значение поля
            $phoneField->setValues(
                (new MultitextCustomFieldValueCollection())
                    ->add(
                        (new MultitextCustomFieldValueModel())
                            ->setEnum('WORKDD')
                            ->setValue($contact['phone'])
                    )
            );
        }

        if (array_key_exists('email',$contact)) {
            $emailField = (new MultitextCustomFieldValuesModel())->setFieldCode('EMAIL');
            $customFields->add($emailField);
            //Установим значение поля
            $emailField->setValues(
                (new MultitextCustomFieldValueCollection())
                    ->add(
                        (new MultitextCustomFieldValueModel())
                            ->setEnum('WORK')
                            ->setValue($contact['email'])
                    )
            );
        }

        if (array_key_exists('weight',$contact)) {
            $field = (new TextCustomFieldValuesModel())->setFieldId(795421);
            $customFields->add($field);
            //Установим значение поля
            $field->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($contact['weight']))
            );
        }
        if (array_key_exists('height',$contact)) {
            $field = (new MultitextCustomFieldValuesModel())->setFieldId(795423);
            $customFields->add($field);
            //Установим значение поля
            $field->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($contact['height']))
            );
        }
        if (array_key_exists('birthday',$contact)) {
            $field = (new DateCustomFieldValuesModel())->setFieldId(795419);
            $customFields->add($field);
            //Установим значение поля
            $field->setValues(
                (new DateCustomFieldValueCollection())
                    ->add((new DateCustomFieldValueModel())->setValue($contact['birthday']))
            );
        }

        $amocrm_contact->setCustomFieldsValues($customFields);

        return $amocrm_contact;
    }

###
    private function fillData(): void
    {
        //воронка
        $this->lead->setPipelineId(self::PIPELINE_ID);
        //название
        $this->lead->setName($this->title);
        //контакты
        $this->lead->setContacts((new ContactsCollection())->add((new ContactModel())->setId($this->contactId)));
        //бюджет
        if ($this->budget)
            $this->lead->setPrice($this->budget);
        //устанавливаем тег
        if ($this->tags)
            $this->lead->setTags((new TagsCollection())->add((new TagModel())->setName($this->tags)));
        //customFields
        $this->leadCustomFieldsValues = new CustomFieldsValuesCollection();
        //Примечание
        if ($this->comments) {
            $this->addCustomFieldsText($this->customFieldsAmoCRM['comments'], $this->comments);
        }
        //utm метки
        if ($this->utm_source)
            $this->addCustomFieldsText($this->customFieldsAmoCRM['utm_source'], $this->utm_source);
        if ($this->utm_campaign)
            $this->addCustomFieldsText($this->customFieldsAmoCRM['utm_campaign'], $this->utm_campaign);
        if ($this->utm_medium)
            $this->addCustomFieldsText($this->customFieldsAmoCRM['utm_medium'], $this->utm_medium);
        if ($this->utm_content)
            $this->addCustomFieldsText($this->customFieldsAmoCRM['utm_content'], $this->utm_content);
        if ($this->utm_term)
            $this->addCustomFieldsText($this->customFieldsAmoCRM['utm_term'], $this->utm_term);

        if ($this->qty)
            $this->addCustomFieldsText($this->customFieldsAmoCRM['totalCount'], $this->qty);

        if ($this->dateTimeSlot)
            $this->addCustomFieldsDate($this->customFieldsAmoCRM['timeSlot'], $this->dateTimeSlot);

        if ($this->cars) {
            foreach ($this->cars as $car) {
                if ($car['carType']->amocrm_field_id) {
                    $this->addCustomFieldsText($car['carType']->amocrm_field_id, $car['qty']);
                }
            }
        }

        $this->lead->setCustomFieldsValues($this->leadCustomFieldsValues);

    }

    /**
     * Некоторые товарищи используют неколько полей для одного значения
     * @param $customFieldId
     * @param string $value
     */
    private function addCustomFieldsText($customFieldId, string $value): void
    {
        if (is_array($customFieldId)) {
            foreach ($customFieldId as $item) {
                $textCustomFieldValueModel = new TextCustomFieldValuesModel();
                $textCustomFieldValueModel->setFieldId($item);
                $textCustomFieldValueModel->setValues(
                    (new TextCustomFieldValueCollection())
                        ->add((new TextCustomFieldValueModel())->setValue($value))
                );
                $this->leadCustomFieldsValues->add($textCustomFieldValueModel);
            }
        } else {
            $textCustomFieldValueModel = new TextCustomFieldValuesModel();
            $textCustomFieldValueModel->setFieldId($customFieldId);
            $textCustomFieldValueModel->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($value))
            );
            $this->leadCustomFieldsValues->add($textCustomFieldValueModel);
        }
    }
    /**
     * Некоторые товарищи используют неколько полей для одного значения
     * @param $customFieldId
     * @param string $value
     */
    private function addCustomFieldsDate($customFieldId, string $value): void
    {
        if (is_array($customFieldId)) {
            foreach ($customFieldId as $item) {
                $textCustomFieldValueModel = new DateCustomFieldValuesModel();
                $textCustomFieldValueModel->setFieldId($item);
                $textCustomFieldValueModel->setValues(
                    (new DateCustomFieldValueCollection())
                        ->add((new DateCustomFieldValueModel())->setValue($value))
//                        ->add((new DateCustomFieldValueModel())->setValue(new \DateTime($value)))
                );
                $this->leadCustomFieldsValues->add($textCustomFieldValueModel);
            }
        } else {
            $textCustomFieldValueModel = new DateCustomFieldValuesModel();
            $textCustomFieldValueModel->setFieldId($customFieldId);
            $textCustomFieldValueModel->setValues(
                (new DateCustomFieldValueCollection())
                    ->add((new DateCustomFieldValueModel())->setValue($value))
            );
            $this->leadCustomFieldsValues->add($textCustomFieldValueModel);
        }
    }

    private function addSelectFieldsText(int $customFieldId, int $value): void
    {
        $selectCustomFieldValueModel = new SelectCustomFieldValuesModel();
        $selectCustomFieldValueModel->setFieldId($customFieldId);
        $selectCustomFieldValueModel->setValues((new SelectCustomFieldValueCollection())->add((new SelectCustomFieldValueModel())->setEnumId($value)));
        $this->leadCustomFieldsValues->add($selectCustomFieldValueModel);
    }

    public function getNotes(): ?NotesCollection
    {
        if ($this->notes) {
            $notes = new NotesCollection();
            $note = new CommonNote();
            $note->setEntityId($this->lead->getId());
            $text = '';
            foreach ($this->notes as $item) {
                $text .= $item . PHP_EOL;
            }
            if ($text) {
                $note->setText($text);
            }
            $notes->add($note);
            return $notes;
        }
//        $notes=new NotesCollection();
//        $note=new CommonNote();
//        $note->setEntityId($this->lead->getId());
//        $text='';
//        if ($this->_order->items) {
//            $slotId=null;
//            foreach ($this->_order->items as $item) {
//                if ($item->slot_id!=$slotId) {
//                    $text.='Заезд: '. $item->slot->getName().PHP_EOL;
//                    $slotId=$item->slot_id;
//                }
//                $text.='Машина: '.$item->carType->name.' Кол-во: '. $item->qty . '. Цена: '.$item->price . '. Итого: '. $item->total . PHP_EOL;
//            }
//        }
//        if ($text) {
//            $note->setText($text);
//        }
//        $notes->add($note);
//        return $notes;
//    }
    }
}