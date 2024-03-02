<?php

namespace booking\useCases\manage\AmoCRM;

use booking\entities\AmoCRM\Credential;
use booking\forms\AmoCRM\CredentialForm;
use booking\repositories\CredentialRepository;
use booking\services\TransactionManager;
use booking\useCases\AmoCRM\AmoCRMService;

class CredentialManageService
{
    private CredentialRepository $repository;
    private TransactionManager $transaction;
    private AmoCRMService $amoCRMService;

    public function __construct(
        CredentialRepository   $credentialRepository,
        TransactionManager $transaction
//        AmoCRMService $amoCRMService
    )
    {
        $this->repository = $credentialRepository;
        $this->transaction = $transaction;
//        $this->amoCRMService = $amoCRMService;
    }

    public function create(CredentialForm $form): Credential
    {
        $this->guardCanCreate();
        $entity = Credential::create(
            $form
        );
        $this->repository->save($entity);

        $this->amoCRMService->setCredential();
        return $entity;
    }

    public function edit(int $id,CredentialForm $form):void
    {
        $entity=$this->repository->get($id);
        $this->guardCanEdit($entity);
        $entity->edit(
            $form
        );
        $this->repository->save($entity);
    }

    public function remove(int $id): void
    {
        $entity = $this->repository->get($id);
        $this->guardCanRemove($entity);
        $this->repository->remove($entity);
    }

###guards

    /**
     * Можно создать только один доступ
     * @param bool $return
     * @return bool
     */
    public static function guardCanCreate(bool $return=false):bool
    {
        if (CredentialRepository::find_st(Credential::MAIN_ID)) {
            if ($return) return false;
            throw new \DomainException('Можно создать только один доступ');
        }
        return true;
    }
    /**
     * Условия когда может просматривать:

     *
     * @return bool
     */
    public static function guardCanView($entityOrId, bool $return=false):bool
    {
        return true;
    }

    /**
     * Условия когда может редактировать:

     */
    public static function guardCanEdit($entityOrId, bool $return=false):bool
    {
        return true;
    }

    /**

     */
    public static function guardCanRemove( $entityOrId, bool $return=false):bool
    {
        return true;

    }


###private



}