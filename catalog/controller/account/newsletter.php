<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerAccountNewsletter extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/newsletter', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/newsletter');

		$this->document->setTitle('Сообщения');
		$this->document->setRobots('noindex,follow');
		$this->document->addStyle('catalog/view/theme/atvdoc/stylesheet/account-additional.css');
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->load->model('account/customer');

			$this->model_account_customer->editNewsletter($this->request->post['newsletter']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('account/account', '', true));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => 'Сообщения',
			'href' => $this->url->link('account/newsletter', '', true)
		);

		$data['action'] = $this->url->link('account/newsletter', '', true);
		
		//Сообщения пользователю
		$this->load->model('account/messages');
		$results = $this->model_account_messages->getUserMessages();
		
		$data['newsletter'] = $this->customer->getNewsletter();
		
		$num = 0;
		foreach ($results as $message) {
			$num++;
			$data['messages'][] = array(
				'title' => $message['title'],
				'message' => html_entity_decode($message['message'], ENT_QUOTES, 'UTF-8'),
				'id' => $message['id'],
				'date_added' => $message['date_added'],
				'num' => $num,
				
			);
		}


		$data['back'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/newsletter', $data));
	}
}