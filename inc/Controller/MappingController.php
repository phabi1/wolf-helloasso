<?php

namespace Wolf\HelloAsso\Controller;

use Wolf\Core\Mvc\Controller\AbstractController;

class MappingController extends AbstractController {
    public function synchronizeAction($request)
    {
        $params = $request->get_json_params();

        $result = $this->getService('wolf.use_case_bus')->execute('wolf-helloasso.synchronize_event', $params);

        return $result;
    }
}