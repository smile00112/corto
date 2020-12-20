<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerInformationFaq extends Controller {
	public function index() {
		$this->load->language('information/information');

		$this->load->model('catalog/information');

		$data['breadcrumbs'] = array();

		// $data['breadcrumbs'][] = array(
		// 	'text' => $this->language->get('text_home'),
		// 	'href' => $this->url->link('common/home')
		// );
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
			$this->document->setRobots('noindex,follow');
		} else {
			$page = 1;
		}
		$limit = 20;
		$filter = array(
			'start'              => ($page - 1) * $limit,
			'limit'              => $limit
		);

		$faqes = $this->model_catalog_information->getFaqes($filter);
	 	$faq_total = $this->model_catalog_information->faqesCount();

		 if ($faqes) {
			$data['faqes'] = $faqes;
			foreach($data['faqes'] as &$f){
				$f['gbcomment']= strip_tags(html_entity_decode($f['gbcomment']));
				$f['gbtext']= strip_tags(html_entity_decode($f['gbtext']));

			}

			$title = 'Вопрос-ответ';
			$data['heading_title'] = $title;
			$this->document->setDescription($title.' - Corto');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('configblog_article_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('information/faq',  $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$pagination = new Pagination();
			$pagination->total = $faq_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('information/faq',  $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($faq_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($faq_total - $limit)) ? $faq_total : ((($page - 1) * $limit) + $limit), $faq_total, ceil($faq_total / $limit));

			$data['limit'] = $limit;


			//$data['description'] = html_entity_decode($faq_total['description'], ENT_QUOTES, 'UTF-8');

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('information/faq', $data));
		} else {
			exit;

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('information/information', 'information_id=' . $information_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
	public function add_comment() {

		$json = [];
		if(!empty($this->request->post['fio'])){ return false; }

		if($this->request->post['hxz5umoo'] != 6){ $json['error'][]= '<p>Неправильный ответ</p>'; }
		if($this->request->post['gbname'] == ''){ $json['error'][]= '<p>Введите имя</p>'; }
		if($this->request->post['gbmail'] == ''){ $json['error'][]= '<p>Введите email</p>'; }
		if($this->request->post['gbtext'] == ''){ $json['error'][]= '<p>Введите сообщение</p>'; }



		if(empty($json['error'])){
			
			$this->load->model('catalog/information');

			$this->model_catalog_information->add_faq($this->request->post);

			//Оповещение манагера
			$manager_emails = $this->model_catalog_information->get_managers_emails($this->request->post);
			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			//$mail->setTo();
			//$mail->setFrom($this->config->get('config_email'));
			$mail->setFrom('fromsite@cortobike.ru');
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject('Новый вопрос на сайте Cortobike');
			$mail->setText($this->load->view('mail/message', $this->request->post));
			//$mail->send(); 
			$emails = explode(',', $this->config->get('config_mail_alert_email'));
			foreach ($emails as $email) {
				if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
			foreach ($manager_emails as $email) {
				if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
			$json['success'] = 'Ваш вопрос отправлен и появится на сайте после модерации';
		}else{
			$json['error'] = implode(' ', $json['error']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	public function send_message() { 
		$json = [];
		if(!empty($this->request->post['fio'])){ return false; }

		//if($this->request->post['hxz5umoo'] != 6){ $json['error'][]= '<p>Неправильный ответ</p>'; }
		if($this->request->post['gbname'] == ''){ $json['error'][]= '<p>Введите имя</p>'; }
		if($this->request->post['gbmail'] == ''){ $json['error'][]= '<p>Введите email</p>'; }
		if($this->request->post['gbtext'] == ''){ $json['error'][]= '<p>Введите сообщение</p>'; }

		if(empty($json['error'])){
		
			$this->load->model('catalog/information');

			$manager_emails = $this->model_catalog_information->get_managers_emails($this->request->post);
			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			//$mail->setTo();
			//$mail->setFrom($this->config->get('config_email'));
			$mail->setFrom('fromsite@cortobike.ru');
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject('Сообщение с сайта Cortobike');
			$mail->setText($this->load->view('mail/message', $this->request->post));
			//$mail->send(); 
			$emails = explode(',', $this->config->get('config_mail_alert_email'));
			foreach ($emails as $email) {
				if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
			foreach ($manager_emails as $email) {
				if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
			$json['success'] = 'Ваше сообщение отправлено';
		}else{
			$json['error'] = implode(' ', $json['error']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function agree() {
		$this->load->model('catalog/information');

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		$output = '';

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			$output .= html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
		}

		$this->response->setOutput($output);
	}
}