<?php
require_once('../../include/conn.php');

$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2017103009610086",

		//商户私钥
		'merchant_private_key' => "MIIEpAIBAAKCAQEAwqGwCMur+H2TK8WGR/K/ceGfzCj0TpWUcDA/cFslgxOFcIMNm+uk2pBkXYOOSLwKW7BwuT91tFxSoJd0c4vJ/9VhEd915a7qd6s2cGwPoIdpkRUUDVEojMQeS2cFKLCI9w5+LJNeqKyje3LKIdabU5JdpubS6F1NU0dgBZSnfRrkpW3tokeW96MLJdATSzvt+KinCJw5yj3LvwK+mzvO5l/u3zMEsIPqcsW1xIiYJaTi4FHHxv8Surjrtbrfyw1cvfz1h/cxKTIsf+cjY/dX2oSdyCHEMY6KzJknqdNnc2lZ0L2/sPsw5pq8NqhHe4Q0Qy3k6CpQVuYClnhL7X/ZuQIDAQABAoIBAADX6odfOpONF8IszPQq3SGFNMgRtV1R0J7b1qqJ+pOi1gUyzDBMyR4ZWhX9p/QwvGrBTPvAhqYNcF45yctmY620IwOM0Fn6dBGosEe0cCzl6VMzoC6el2pkvyzceW0/tpg7e9h9mv9oIB081htnbqziCX3bqjZPkTI3MPdVOZSVM1BMempwCso3aCSk9xdJHQxV1MaHbx8mdUuyQIeOy0RTNPTpsAWZ9XraN8inl4CaXrZ4m8y0CaQdT4eSI3Hlhnv3GkMaUzhW27XUgq0MLIseTH6MkARB+2DNVHHA7JHujijTeb2qZeggdJa681M/aps7OVRmrQkyctOdZM27xrECgYEA7Qf5sTr7FuMxjL56uYW7JC2SP2/wQVFX2rXQaCpSv2oKJZ6hwfNVN/D+FAVRZJEcZbP5FjquhbVhTCuNnypRfKCwc1BEnbbR9cRr0sRjrsyrBgPUeeJ6G6CEaC2bcXhKl5/Exh9cEcmvk6JvfARyDSjbbLwf8MNNCeuFcAJKyhcCgYEA0jUUCubi1JREGWZVltdY003mh7ju/VqhCFVSlbYG9wpUoXt6yMnRx2jQEomxpsjuED78gX4J7jxfJ8JdA0zuAtPBfJ+7TIm96mS/XaQ4FdxqKtVgnH/B1TndijoVamR40SvkogFFXb0K9tWJ3x4Qe0BVkb/dz3c3mQ+1L6KSbK8CgYEAmq9WutuD2xbfUjtIeeQVEfIPfAjAINBF5jw6yo+8OqCoCI7yf1Eebwh9t3EVAe1VO+Xsix42oJmS6pONs655STzQiiB69vXFJX9O4lg5uWxlu+Ip+nTbPUZOwnHo/G8FNtgUITt76TvWsiESYjvAdmgQcnB+46+AihXCmitUxXcCgYBjv7I6FiQPUigakXgN8zSAWNgLEZO+/9GofayJxG37rphIZKr4OuTkJnWORm7ZwUFOzkTOQfv+ZAGXgWDX3xqWltubAsVDZZ2Ma7BvhCzlGsn6d3S37twRDbt2C2f8K581SwncYpQDDmD0jpI1ITQa+IVie1SLTXxBkrWc3B11mwKBgQDXC918POWP8usgLiCgZK9YW7S6oFwLGG+R8IwvsxHSgXhxNtOpqh38WqciSzPi62TWfjDBcsbJRTf2rvg3vH/SNO0SxQpRiy/cKl+lXwF+9FjYpw9U8JtXUmZwQjSmDJt2ldkk9iZwvzHinNE8vjXeIqDVMG5tI1pXOZ7D0z1+Ow==",
		
		//异步通知地址
		'notify_url' => 'http://'.WEB_DOMAIN.'/onlinepay/alipay/notify_url.php',
		
		//同步跳转
		'return_url' => 'http://'.WEB_DOMAIN.'/onlinepay/alipay/return_url.php',

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzoT7obMtfg/hlTIsx2s/MZcd/G3Hni66mWJySUK+g6WQ+mP01sp8XnWGB3RUu8SWpnOBUg2xQmR4l5JhLeaa6L/aCRR2P561e4w2F0ZkOweHI8xvo2u3QLXbtwKjHPGI5ShgMBkQVT9+fwHEkje/2LmPhGGwoZen4Pe/IyE6+NtScWpEBeF2sjYd4QKvg42bDX+n5uDpLMeTWi+iDJU3Ue/HhMhLebPxn5EYgbpVeKXZeP00+eT1JziBLwmxbygf2/ILGhVVx5cA76NSJ+789l6HxVuFzhNh8og2WqQKP2FnlM6eZ36tEA0qMRv9+eiVAI5AsEkLGdSJ1ryISBOKfwIDAQAB",
);
