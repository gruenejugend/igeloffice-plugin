<?php

	function io_init() {
		io_groups::register();
		io_permission::register();
		io_request::register();
	}