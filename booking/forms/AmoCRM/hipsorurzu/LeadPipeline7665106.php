<?php

namespace booking\forms\AmoCRM\hipsorurzu;


use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\TagsCollection;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\SelectCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\SelectCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\SelectCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\TagModel;
use booking\forms\AmoCRM\LeadForms;
use function Symfony\Component\Translation\t;

class LeadPipeline7665106 extends LeadForms
{
    const PIPELINE_ID=7665106;
    public ?LeadModel $lead;
    public string $title;
    public ?string $contact_name;                //Имя контакта
    public ?string $contact_secondName;         //Отчество контакта
    public ?string $contact_lastName;           //Фамилия контакта
    public ?string $contact_telephone;          //Телефон контакта
    public ?string $contact_email;              //Email контакта
    public ?float $budget;                      //Бюджет
    public ?string $comments;                    //Комментарий к сделке(сообщение формы)
    public ?string $utm_source;                 //utm_source
    public ?string $utm_medium;                 //utm_medium
    public ?string $utm_campaign;               //utm_campaign
    public ?string $utm_content;                //utm_content
    public ?string $utm_term;                   //utm_term
    public ?string $tags;                 //теги
    public int $dateTimeSlot;                       //дата и время заезда
    public int $typeSlot;                       //Тип заезда(детский, взрослый, клубный)


    public array $customFieldsAmoCRM = [        //соответсвтие название и коду по амоцрм
        'utm_source'=>[273553,122825],
        'utm_medium'=>[273555,122827],
        'utm_campaign'=>[273557,122829],
        'utm_term'=>[273559,122833],
        'utm_content'=>[273561,122831],
        'utm_referrer'=>273563,
        'UTM'=>705095,
        'comments'=>701479,                       //примечание
        'sourceId'=>697153                      //источник сделки
    ];


    public $leadCustomFieldsValues;

    public function __construct(
        string $title,
        ?string $contact_name=null,
        ?string $contact_secondName=null,
        ?string $contact_lastName=null,
        ?string $contact_telephone=null,
        ?string $contact_email=null,
        ?float $budget=null,
        ?string $comments=null,
        ?string $utm_source=null,
        ?string $utm_medium=null,
        ?string $utm_campaign=null,
        ?string $utm_content=null,
        ?string $utm_term=null,
        ?string $tags=null
    )
    {
        $this->title=$title;
        $this->contact_name=$contact_name;
        $this->contact_secondName=$contact_secondName;
        $this->contact_lastName=$contact_lastName;
        $this->contact_telephone=$contact_telephone;
        $this->contact_email=$contact_email;
        $this->budget=$budget;
        $this->comments=$comments;
        $this->utm_source=$utm_source;
        $this->utm_medium=$utm_medium;
        $this->utm_campaign=$utm_campaign;
        $this->utm_content=$utm_content;
        $this->utm_term=$utm_term;
        $this->tags=$tags;
    }

    public function getLead(): LeadModel
    {
        $this->lead=parent::getLead();
        $this->fillData();
        return $this->lead;
    }
    public function getContact():ContactModel
    {
        $contact=parent::getContact();
        $name='';
        if ($this->contact_name)
            $name = $this->contact_name;
        if ($this->contact_secondName)
            $name.=' '.$this->contact_secondName;
        if ($this->contact_lastName)
            $contact->setLastName($this->contact_lastName);
        if ($name)
            $contact->setName($name.' '. $this->contact_lastName);
            $contact->setFirstName($name);

        $customFields= new CustomFieldsValuesCollection();
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
###
    private function fillData():void
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
        $this->lead->setTags((new TagsCollection())->add((new TagModel())->setName($this->tags)));
        //customFields
        $this->leadCustomFieldsValues = new CustomFieldsValuesCollection();
        //Примечание
        if ($this->comments) {
            $this->addCustomFieldsText($this->customFieldsAmoCRM['comments'],$this->comments);
        }
        //utm метки
        if ($this->utm_source)
            $this->addCustomFieldsText($this->customFieldsAmoCRM['utm_source'],$this->utm_source);
        if ($this->utm_campaign)
            $this->addCustomFieldsText($this->customFieldsAmoCRM['utm_campaign'],$this->utm_campaign);
        if ($this->utm_medium)
            $this->addCustomFieldsText($this->customFieldsAmoCRM['utm_medium'],$this->utm_medium);
        if ($this->utm_content)
            $this->addCustomFieldsText($this->customFieldsAmoCRM['utm_content'],$this->utm_content);
        if ($this->utm_term)
            $this->addCustomFieldsText($this->customFieldsAmoCRM['utm_term'],$this->utm_term);

        $this->lead->setCustomFieldsValues($this->leadCustomFieldsValues);
    }

    /**
     * Некоторые товарищи используют некольк полея для одного значения
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

    private function addSelectFieldsText(int $customFieldId, int $value):void
    {
        $selectCustomFieldValueModel = new SelectCustomFieldValuesModel();
        $selectCustomFieldValueModel->setFieldId($customFieldId);
        $selectCustomFieldValueModel->setValues((new SelectCustomFieldValueCollection())->add((new SelectCustomFieldValueModel())->setEnumId($value)));
        $this->leadCustomFieldsValues->add($selectCustomFieldValueModel);
    }
}