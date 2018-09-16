<?php

namespace Unific\Extension\Controller\Adminhtml\Request;

use Unific\Extension\Controller\Adminhtml\Request;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

class Save extends Request
{
    /**
     * Save Request
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/request'));
        }

        $requestModel = $this->_objectManager->create('Unific\Extension\Model\Request');
        $id = (int)$request->getParam('id');

        if ($id) {
            $requestModel->load($id);

            // Remove old mappings
            $mappingCollection = $this->_objectManager->create('Unific\Extension\Model\Mapping')->getCollection();
            $mappingCollection->addFieldToFilter('request_id', $id);
            $mappingCollection->walk('delete');

            // Remove old conditions
            $conditionCollection = $this->_objectManager->create('Unific\Extension\Model\ResourceModel\Condition\Collection');
            $conditionCollection->addFieldToFilter('request_id', $id);
            foreach($conditionCollection->getItems() as $item)
            {
                $item->delete();
            }
            // Remove old response mappings
            $responseMappingCollection = $this->_objectManager->create('Unific\Extension\Model\ResourceModel\ResponseMapping\Collection');
            $responseMappingCollection->addFieldToFilter('request_id', $id);
            foreach($responseMappingCollection->getItems() as $item)
            {
                $item->delete();
            }
            // Remove old response conditions
            $responseConditionCollection = $this->_objectManager->create('Unific\Extension\Model\ResourceModel\ResponseCondition\Collection');
            $responseConditionCollection->addFieldToFilter('request_id', $id);
            foreach($responseConditionCollection->getItems() as $item)
            {
                $item->delete();
            }        }

        try {
            $data = $request->getParams();

            $requestModel->addData($data);
            $requestModel->save();

            // Save the mapping information
            if (isset($data['request_mapping']) && is_array($data['request_mapping'])) {
                foreach ($data['request_mapping']['value'] as $option => $valueData) {
                    if($data['request_mapping']['delete'][$option] != 1)
                    {
                        $typeModel = $this->_objectManager->create('Unific\Extension\Model\Mapping');
                        $typeModel->setRequestId($requestModel->getId());
                        $typeModel->addData($valueData);


                        if ($valueData['internal'] == "") {
                            $typeModel->setInternal($valueData['internal_select']);
                        }

                        $typeModel->setInternal($valueData['internal_type'] . '.' . $typeModel->getInternal());
                        $typeModel->save();
                    }
                }
            }

            // Save the mapping information
            if (isset($data['request_condition']) && is_array($data['request_condition'])) {
                foreach ($data['request_condition']['value'] as $option => $valueData) {
                    if($data['request_condition']['delete'][$option] != 1)
                    {
                        $value = (isset($valueData['value']) && $valueData['value'] != '') ? $valueData['value'] : $valueData['select_value'];

                        $conditionData = array(
                            'condition_order' => $data['request_condition']['order'][$option],
                            'condition' => $valueData['type'],
                            'condition_comparison' => $valueData['comparison'],
                            'condition_value' => $value,
                            'condition_action' => $valueData['action'],
                            'condition_action_params' => json_encode($valueData['action_params'])
                        );

                        $typeModel = $this->_objectManager->create('Unific\Extension\Model\Condition');
                        $typeModel->setRequestId($requestModel->getId());
                        $typeModel->addData($conditionData);
                        $typeModel->save();
                    }
                }
            }

            // Save the response mapping information
            if (isset($data['response_mapping']) && is_array($data['response_mapping'])) {
                // Remove old mappings
                $typeInstance = $this->_objectManager->get('Unific\Extension\Model\ResourceModel\ResponseMapping\Collection');
                $typeInstance->addFieldToFilter('request_id', $requestModel->getId());

                foreach ($typeInstance->getItems() as $item) {
                    $item->delete();
                }

                foreach ($data['response_mapping']['value'] as $option => $valueData) {
                    if($data['response_mapping']['delete'][$option] != 1) {
                        $typeModel = $this->_objectManager->create('Unific\Extension\Model\ResponseMapping');
                        $typeModel->setRequestId($requestModel->getId());
                        $typeModel->addData($valueData);

                        if ($valueData['internal'] == "") {
                            $typeModel->setInternal($valueData['internal_select']);
                        }

                        $typeModel->setInternal($valueData['internal_type'] . '.' . $typeModel->getInternal());

                        $typeModel->save();
                    }
                }
            }

            // Save the mapping information
            if (isset($data['response_condition']) && is_array($data['response_condition'])) {
                // Remove old mappings
                $typeInstance = $this->_objectManager->get('Unific\Extension\Model\ResourceModel\ResponseCondition\Collection');
                $typeInstance->addFieldToFilter('request_id', $requestModel->getId());

                foreach ($typeInstance->getItems() as $item) {
                    $item->delete();
                }

                foreach ($data['response_condition']['value'] as $option => $valueData) {
                    if($data['response_condition']['delete'][$option] != 1) {

                        $value = (isset($valueData['value']) && $valueData['value'] != '') ? $valueData['value'] : $valueData['select_value'];

                        $conditionData = array(
                            'condition_order' => $data['response_condition']['order'][$option],
                            'condition' => $valueData['type'],
                            'condition_comparison' => $valueData['comparison'],
                            'condition_value' => $value,
                            'condition_action' => $valueData['action'],
                            'condition_params' => json_encode($valueData['action_params'])
                        );

                        $typeModel = $this->_objectManager->create('Unific\Extension\Model\ResponseCondition');
                        $typeModel->setRequestId($requestModel->getId());
                        $typeModel->addData($conditionData);
                        $typeModel->save();
                    }
                }
            }

            $this->messageManager->addSuccess(__('The request has been saved.'));
            $this->_getSession()->setFormData(false);

        } catch (LocalizedException $e) {
            $this->messageManager->addError(nl2br($e->getMessage()));
            $this->_getSession()->setData('extension_request_form_data', $this->getRequest()->getParams());
            return $resultRedirect->setPath('*/*/edit', ['group' => $request->getParam('group'), 'id' => $requestModel->getId(), '_current' => true]);
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving this request.'));

            $this->_getSession()->setData('extension_request_form_data', $this->getRequest()->getParams());
            return $resultRedirect->setPath('*/*/edit', ['group' => $request->getParam('group'), 'id' => $requestModel->getId(), '_current' => true]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
