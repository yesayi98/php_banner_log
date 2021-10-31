<?php

namespace HTTP\Controllers;

use Base\App;
use Base\Context;
use Base\Request;
use Base\Response;
use Exception;
use Models\Visitor;

class IndexController
{
    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function index(Request $request): Response
    {
        if (empty($request->getHeader('Referer'))){
            return response('Not allowed', 500);
        }

        $visitor = Visitor::get(new Context(['*'], [
            'ip_address' => $request->getIp(),
            'user_agent' => $request->getHeader('User-Agent'),
            'page_url' => $request->getHeader('Referer')
        ]))[0];

        if (!$visitor) {
            $visitor = new Visitor();
        }

        $visitor->ip_address = $request->getIp();
        $visitor->page_url = $request->getHeader('Referer') ?? '';
        $visitor->user_agent = $request->getHeader('User-Agent');
        $visitor->view_date = date('Y-m-d');

        if (isset($visitor->views_count)) {
            $visitor->views_count++;
        }else{
            $visitor->views_count = 1;
        }

        $connection = App::get('db_connection');

        try {
            $connection->beginTransaction();
            $visitor->save();

            $connection->exec('LOCK TABLES ' . $visitor->getTable() . ' WRITE');

            $connection->commit();

            $connection->exec('UNLOCK TABLES');
        } catch (\Throwable $exception){
            $connection->rollback();

            return response([$exception->getMessage(), $exception->getFile(), $exception->getLine()]);
        }

        return response(assets('images/banner.jpg'));
    }
}