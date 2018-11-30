<?php

namespace App\Controller;

use App\Entity\ApiTable;
use App\CodeGenerator;
use App\Repository\ApiTableRepository;
use App\XlsGenerator;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ApiController extends FOSRestController
{
    /**
     * @Route("/generate", name="generate", methods={"POST"})
     * @RequestParam(name="nb", requirements="\d+", default="1", description="Count codes.")
     * @RequestParam(name="export", requirements="xls", default=null, nullable=true, description="Export type.")
     * @throws \Exception
     */
    public function generateAction(ParamFetcher $paramFetcher, CodeGenerator $codeGenerator, ApiTableRepository $repository, XlsGenerator $xlsGenerator)
    {
        $nb = $paramFetcher->get('nb');
        $export = $paramFetcher->get('export');
        $codes = [];
        for ($i = 0; $i < $nb; $i++) {
            $code = $codeGenerator->generateCode();
            if ($this->codeExists($code, $repository)) {
                $i--;
                continue;
            }
            $date = new \DateTime('now', new \DateTimeZone('UTC'));
            $this->create($code, $date);
            $codes[] = $code;
        }

        if ($export) {
            $xlsGenerator->createTmpFile($codes);
            $tempFile = $xlsGenerator->getTempFile();
            $fileName = $xlsGenerator->getFileName();
            return $this->file($tempFile, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
        }
        return $this->json($codes);
    }
    /**
     * @Route("/{code}", requirements={"code"="\w+"}, name="code", methods={"GET"})
     * @throws \Exception
     */
    public function codeAction($code, ApiTableRepository $repository)
    {
        try{
            $apiTable = $repository->findOneBy(['code' => $code]);
            if ($apiTable) {
                return $this->json([
                    'id' => $apiTable->getId(),
                    'code' => $apiTable->getCode(),
                    'date' => $apiTable->getDate(),
                ]);
            }
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
        throw $this->createNotFoundException('Code not found');
    }

    public function codeExists($code, ApiTableRepository $repository): bool
    {
        if ($repository->findOneBy(['code' => $code])) {
            return true;
        }
        return false;
    }

    public function create(string $code, \DateTime $date): void
    {
        $data = new ApiTable();
        $data->setCode($code);
        $data->setDate($date);
        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();
    }
}