<?php

namespace AnthemCM\Feedback\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AnthemCM\Feedback\Form\FeedbackSubmitForm;

/**
 * Feedback front-end controller.
 */
class FeedbackController
{
 /**
  * The constructor.
  *
  * @param \Silex\Application $app
  */
  public function __construct(Application $app)
  {
    $this->app = $app;
  }

  public function feedbackAction(Request $request)
  {
    $form = new FeedbackSubmitForm($this->app);
    $message = '';

    if ($request->getMethod() == 'POST')
    {
      $form->setValue($request->request->all());
      $valid = $form->validate();
      if ($valid)
      {
        $feedback = $form->save();
        $this->app['feedback.model']->save($feedback);

        // Mail a message
        if (!empty($this->app['Feedback']['feedback_to']))
        {
          $mail = \Swift_Message::newInstance();
          $mail->setFrom($feedback->getEmail() ?: array($this->app['Core']['mail.default_from']));
          $mail->setTo($this->app['Feedback']['feedback_to']);
          $mail->setSubject(_t('Feedback.MAIL_SUBJECT', $this->app['Core']['project']));
          $mail->setBody($this->app['core.view']->render('AnthemCM/Feedback:mail/feedback.php', array(
            'user'    => $feedback->getUser(),
            'email'   => $feedback->getEmail(),
            'message' => $feedback->getContent(),
          )), 'text/html');
          $this->app['mailer']->send($mail);
        }

        $message = _t('Feedback.SUBMITTED');
      }
    }

    return new Response(json_encode(array(
      'form'    => $form->render(),
      'message' => $message,
    )), 200, array('Content-Type' => 'application/json'));
  }
}
