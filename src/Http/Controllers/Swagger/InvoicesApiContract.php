<?php

namespace EscolaLms\Invoices\Http\Controllers\Swagger;

use EscolaLms\Invoices\Http\Requests\InvoicesReadRequest;
use Illuminate\Http\Response;

interface InvoicesApiContract
{
    /**
     * @OA\Get(
     *     path="/api/questionnaire/{model_type_title}/{model_id}/{id}",
     *     summary="Read a questionnaire identified by a given id identifier and model",
     *     tags={"Questionnaire"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
     *         name="model_type_title",
     *         description="Name of Model (Course, Webinar etd.)",
     *         @OA\Schema(
     *            type="string",
     *         ),
     *         required=true,
     *         in="path"
     *     ),
     *     @OA\Parameter(
     *         name="model_id",
     *         description="id of Model (Course, Webinar etd.)",
     *         @OA\Schema(
     *            type="integer",
     *         ),
     *         required=true,
     *         in="path"
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         description="id of Questionnaire",
     *         @OA\Schema(
     *            type="integer",
     *         ),
     *         required=true,
     *         in="path"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\JsonContent(ref="#/components/schemas/Questionnaire")
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="endpoint requires authentication",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="user doesn't have required access rights",
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="server-side error",
     *      ),
     * )
     */
    public function read(InvoicesReadRequest $request): Response;
}
