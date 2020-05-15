<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Entity\MapPoint;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CovoiturageController extends AbstractController
{
    /**
     * @Route("/covoiturage", name="homeCovoiturage")
     */
    public function index()
    {
        return $this->render('covoiturage/index.html.twig', [
            'controller_name' => 'CovoiturageController',
        ]);
    }




    /**
     * @Route("/covoiturage/add", name="AddCovoiturage")
     */
    public function add(Request $request)
    {
//        dd(json_decode('{"xxx":"yyy"}',true));

        $covoiturage = new Covoiturage();
        $manager = $this->getDoctrine()->getManager();
        $user = $manager->getRepository('App:User')->findOneById($this->getUser()->getId());
        $covoiturage->setOwner($user);

        if (($request->get('modifyId'))) {
            $covoiturage = $manager->getRepository('App:Covoiturage')->findOneById($request->get('modifyId'));
            if ($covoiturage && $covoiturage->getOwner() == $this->getUser()) {
                $points = [];
                foreach ($covoiturage->getMapPoints() as $point) {
                    array_push($points, $point);

                }

                $jsonPoints = json_encode($points);
                foreach ($covoiturage->getMapPoints() as $point) {
                    $covoiturage->removeMapPoint($point);

                }

            } else {
                $this->addFlash('error', 'you don\'t own this covoiturage !');
                return $this->redirect('/covoiturage/show');
            }
        };


        $form = $this->createFormBuilder($covoiturage)
            ->add('departurePoint', TextType::class, [
                "attr" => [
                    "placeholder" => "Location name",
                ]
            ])
            ->add('arrivalPoint', TextType::class, [
                "attr" => [
                    "placeholder" => "location name",
                ]
            ])
            ->add('type', ChoiceType::class, [
                "choices" => [
                    "one way" => "oneWay",
                    "two way" => "twoWay"
                ],
                "attr" => [
                    "onclick" => "checkType()"
                ]
            ])
            ->add('departureTime', TimeType::class, [
                'input' => 'timestamp',
                'widget' => 'choice',
            ])
            ->add('returnTime', TimeType::class, [
                'input' => 'timestamp',
                'widget' => 'choice',

            ])
            ->add('moreDetails', TextareaType::class, [
                "required" => false
            ]);
        if (($request->get('modifyId')))
            $form=$form->add('modify offer', SubmitType::class, [
                "attr" => [
                    "onclick" => 'jsonPoints()'

                ]
            ])->getForm();
        else
            $form=$form->add('add offer', SubmitType::class, [
                "attr" => [
                    "onclick" => 'jsonPoints()'

                ]
            ])
            ->getForm();


        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('type')->getViewData() == 'oneWay')
                $covoiturage->setReturnTime(null);


//            dd($covoiturage->getMoreDetails());
            $points = json_decode($_POST['points'], true);
            foreach ($points as $element) {
                $point = new MapPoint();
                $point->setX($element['x']);
                $point->setY($element['y']);
                $point->setCovoiturage($covoiturage);
                $manager->persist($point);

            }

            $manager->persist($covoiturage);
            $manager->flush();
            if (isset($jsonPoints)) {
                $this->addFlash('success', 'offre covoiturage modifié avec succés');
                return $this->redirect('/covoiturage/myCovoiturage');
            }
            $this->addFlash('success', 'offre covoiturage ajouté avec succés');

            return $this->redirect('/covoiturage/add');
        }

        if (isset($jsonPoints))

            return $this->render('covoiturage/addCovoiturage.html.twig', [
                'form' => $form->createView(),
                'jsonPoints' => $jsonPoints
            ]);
        else  return $this->render('covoiturage/addCovoiturage.html.twig', [
            'form' => $form->createView()

        ]);
    }




    /**
     * @Route("/covoiturage/show", name="showCovoiturage")
     */
    public function show()
    {
        return $this->render('covoiturage/showCovoiturage.html.twig');
    }




    /**
     * @Route("/covoiturage/getPoints", name="json points")
     */
    public function Points()
    {


        $manager = $this->getDoctrine()->getManager();
        $mapPoints = $manager->getRepository('App:MapPoint')->findAll();
        return new Response(json_encode($mapPoints));


    }




    /**
     * @Route("/covoiturage/getCovoiturage", name="get covoiturage")
     */
    public function getCovoiturage(Request $request)
    {


        if ($request->get('covoiturageId') != null) {
            $manager = $this->getDoctrine()->getManager();
            $covoiturage = $manager->getRepository('App:Covoiturage')->findOneById($request->get('covoiturageId'));
            $owner = $manager->getRepository('App:User')->findOneById($covoiturage->getOwner()->getId());

            return new Response('{"firstName":"' . $owner->getFirstName() . '","lastName":"' . $owner->getLastName() .
                '","email":"' . $owner->getEmail() . '","moreDetails":"' . $covoiturage->getMoreDetails() . '","departurePoint":"' .
                $covoiturage->getDeparturePoint() . '",' . '"arrivalPoint":"' . $covoiturage->getArrivalPoint() . '",'
                . '"type":"' . $covoiturage->getType() . '",' . '"departureTime":"' . $covoiturage->getDepartureTime() . '",'
                . '"returnTime":"' . $covoiturage->getReturnTime() .
                '"}'
            );
        } else return new Response('{}');


    }




    /**
     * @Route("/covoiturage/myCovoiturage", name="myCovoiturage")
     */
    public function myCovoiturage(Request $request)
    {

        $manager = $this->getDoctrine()->getManager();
        $covoiturages = $manager->getRepository('App:Covoiturage')->findByOwner($this->getUser()->getId());


        return $this->render('covoiturage/myCovoiturage.html.twig', [
            "covoiturages" => $covoiturages
        ]);


    }




    /**
     * @Route("/covoiturage/delete/{covoiturageId}", name="deleteCovoiturage")
     */
    public function deleteCovoiturage($covoiturageId)
    {


        $manager = $this->getDoctrine()->getManager();
        $covoiturage = $manager->getRepository('App:Covoiturage')->findOneById($covoiturageId);
        if ($covoiturage->getOwner()->getId() != $this->getUser()->getId())
            $this->addFlash('error', "deletion failed !!");

        else {
            $manager->remove($covoiturage);
            $manager->flush();

        }

        return $this->redirect('/covoiturage/myCovoiturage');


    }

}
