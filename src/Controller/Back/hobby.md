``` PHP

/**
 * @Route("/back/hobby")
 */
class HobbyController extends AbstractController
{
    /**
     * @Route("/", name="app_back_hobby_index", methods={"GET"})
     */
    public function index(HobbyRepository $hobbyRepository, Dog $dog): Response
    {
        return $this->render('back/hobby/index.html.twig', [
            'hobbies' => $hobbyRepository->findAll(),
            'dog' => $dog,
        ]);
    }




    /**
 * @Route("/back/hobby")
 */
class HobbyController extends AbstractController
{
    /**
     * @Route("/", name="app_back_hobby_listhobbies", methods={"GET"})
     */
    public function listHobbies(HobbyRepository $hobbyRepository, Dog $dog, Member $memberRepository): Response
    {
        return $this->render('back/hobby/listhobbies.html.twig', [
            'hobbies' => $hobbyRepository->findAll(),
            'dog' => $dog,
            'member' => $member,
        ]);
    }