-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-04-20 03:22:41
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pingce`
--

-- --------------------------------------------------------

--
-- 表的结构 `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2016_03_13_023038_create_users_table', 1),
('2016_03_14_144004_create_roles_table', 2),
('2016_03_15_015827_create_roles_table', 3),
('2016_03_15_020342_create_users_table', 4),
('2016_03_15_021001_create_papers_table', 5),
('2016_03_15_030602_create_user_paper_table', 6),
('2016_03_16_122355_add_active_to_papers', 7),
('2016_03_16_132741_add_scorer_paper_table', 8),
('2016_03_17_091039_add_details_scorer_paper', 9),
('2016_03_20_154200_add_submit_socrer_paper', 10),
('2016_03_27_163128_add_catetory_papers', 11),
('2016_03_27_193105_add_object_subject_scorer_paper', 12),
('2016_03_27_202733_add_count_scorer_paper', 13),
('2016_03_27_211552_add_status_user_paper', 14),
('2016_03_27_224457_add_time_user_paper', 15),
('2016_03_28_162628_add_comment_scorer_paper', 16),
('2016_04_19_190333_create_news_table', 17),
('2016_04_19_191149_add_publisher_news', 18),
('2016_04_19_193717_add_active_news', 19),
('2016_04_19_214022_create_resources_table', 20);

-- --------------------------------------------------------

--
-- 表的结构 `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `top` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `publisher` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

--
-- 转存表中的数据 `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `top`, `created_at`, `updated_at`, `publisher`, `active`) VALUES
(1, '新闻标题', '我是新闻内容', 1, 1461065221, 1461068613, 'administrator', 0),
(2, '新闻2', '新闻2的内容', 1, 1461065328, 1461065328, 'administrator', 1),
(3, '新闻3', '', 0, 1461067486, 1461067648, 'administrator', 0),
(4, '新闻3', '', 0, 1461067494, 1461067644, 'administrator', 0),
(5, '新闻3', '', 0, 1461067494, 1461067646, 'administrator', 0),
(6, '新闻3', '', 0, 1461067531, 1461067643, 'administrator', 0),
(7, '新闻3', '', 0, 1461067532, 1461067641, 'administrator', 0),
(8, '新闻3', '', 0, 1461067533, 1461067640, 'administrator', 0),
(9, '新闻3', '', 0, 1461067588, 1461067639, 'administrator', 0),
(10, '习近平：让互联网更好造福国家和人民', ' 新华社北京4月19日电 中共中央总书记、国家主席、中央军委主席、中央网络安全和信息化领导小组组长习近平19日上午在京主持召开网络安全和信息化工作座谈会并发表重要讲话，强调按照创新、协调、绿色、开放、共享的发展理念推动我国经济社会发展，是当前和今后一个时期我国发展的总要求和大趋势，我国网信事业发展要适应这个大趋势，在践行新发展理念上先行一步，推进网络强国建设，推动我国网信事业发展，让互联网更好造福国家和人民。\r\n　　 中共中央政治局常委、中央网络安全和信息化领导小组副组长李克强、刘云山出席座谈会。\r\n　　 习近平主持座谈会，他首先表示，我国互联网事业快速发展，网络安全和信息化工作扎实推进，取得显著进步和成绩，同时也存在不少短板和问题。召开这次座谈会，就是要当面听取大家意见和建议，共同探讨一些措施和办法，以利于我们把工作做得更好。\r\n　　 座谈会上，中国工程院院士、中国电子科技集团公司总工程师吴曼青，安天实验室首席架构师肖新光，阿里巴巴集团董事局主席马云，友友天宇系统技术有限公司首席执行官姚宏宇，解放军驻京某研究所研究员杨林，北京大学新媒体研究院院长谢新洲，北京市委网信办主任佟力强，华为技术有限公司总裁任正非，国家计算机网络与信息安全管理中心主任黄澄清，复旦大学网络空间治理研究中心副主任沈逸先后发言。他们分别就实现信息化发展新跨越、加快构建信息领域核心技术体系、互联网企业的国家责任、实现网信军民融合深度发展、发挥新媒体在凝聚共识中的作用、突破信息产业发展和网络安全保障基础理论和核心技术、加强网络信息安全技术能力建设顶层设计等谈了意见和建议。', 1, 1461069141, 1461071859, 'administrator', 1);

-- --------------------------------------------------------

--
-- 表的结构 `papers`
--

CREATE TABLE IF NOT EXISTS `papers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `introduction` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `answer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `publisher` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=27 ;

--
-- 转存表中的数据 `papers`
--

INSERT INTO `papers` (`id`, `name`, `introduction`, `content`, `answer`, `publisher`, `category`, `created_at`, `updated_at`, `active`) VALUES
(1, ' 2015年北京高考历史试卷', '这是2015年北京地区高考历史试卷', 'paper/d90ad7a3c97c002414303a0da5c2d68a.xml', 'answer/9ac634d47efa35ab0e82848dca2c9e7f.xml', 'administrator', '', 1458010519, 1458133452, 0),
(2, ' 2013北京高考地理试卷', '这是 2013北京高考地理试卷', 'paper/dcd97cb17e35314b4568b9e3a2f97739.xml', 'answer/20f0f7e34fbf13820a8ab8f355c61103.xml', 'administrator', '', 1458010655, 1458136587, 0),
(3, ' 地理测试01', '这是地理测试01', 'paper/183a627f0f4168cd7f360397e886dcf7.xml', 'answer/b90d09d1d5e3c28219350a544ef20ab7.xml', 'administrator', '', 1458130510, 1460534271, 0),
(4, ' 地理测试02', '这是北京地区地理测试02', 'paper/90e3f831c9b5fa822306d5cc92a02e52.xml', 'answer/869b2df15ecc65981c3d195fa2fee863.xml', 'administrator', '', 1458457364, 1460534272, 0),
(5, ' 地理测试03', '这是地理测试03', 'paper/dc44c1c5003ceb7d83f7d89955bbe37a.xml', 'answer/b3aa83826aff2cfe22357bf062e468a6.xml', 'administrator', '', 1458458445, 1460534273, 0),
(6, ' 地理测试04', '这是地理测试04', 'paper/7af3819b9973786ad4d078583610e8af.xml', 'answer/fe15a963090f011719417186bae09d1e.xml', 'administrator', '', 1458484378, 1459156337, 0),
(7, '地理_地理测试01', '这是地理_地理测试01', 'paper/a39ceccd6daf09c7ddf982e321da5b0a.xml', 'answer/32ee1fb943625a2e6ab0df01c5ad3816.xml', 'administrator', 'geography', 1459068101, 1460534274, 0),
(12, ' 历史测试01', ' 历史测试01', 'paper/b5dd15eef32486ec41b8922ab2c4e632.xml', 'answer/40f27179d0f591a6bb4c5f1055b81cea.xml', 'administrator', 'history', 1459157125, 1459157452, 0),
(13, ' 地理测试004', '地理测试004', 'paper/445710fe805d8366208a0733136f8292.xml', 'answer/39ebb69f00624a3b9210a6e381b732b1.xml', 'administrator', 'geography', 1459157181, 1459157186, 0),
(16, ' 历史测试01', '这是历史测试01', 'paper/424825f6f6a2c75e262db36f325277ab.xml', 'answer/d6f9914f388efadcb5d0a14584cc58a0.xml', 'administrator', 'history', 1459162689, 1459163069, 0),
(17, ' 地理测试04', ' 地理测试04', 'paper/a7e5ab3ee7bc153c717c65d4dd1ee468.xml', 'answer/26aa42528fe64b182fa6640c42fb6a54.xml', 'administrator', 'geography', 1459162726, 1460534275, 0),
(18, ' 新测试地理测试01', ' 新测试地理测试01', 'paper/c2e62249f6238fb650badb2de824bf23.xml', 'answer/30bc798877332929f169bc35b96ee7f2.xml', 'administrator', 'geography', 1460360494, 1460534276, 0),
(19, ' 新测试历史测试01', '新测试历史测试01', 'paper/607930ca9df768d03e91a2445c2690ef.xml', 'answer/5abbf2e5b2c77c8b9ab25ceffba0fa6e.xml', 'administrator', 'history', 1460364625, 1460534276, 0),
(20, ' 历史测试02', ' 历史测试02', 'paper/8652be99bdf0b2dc2c6be3bc4a4f1d05.xml', 'answer/7a388585d417e2f3b93b98e0f194f443.xml', 'administrator', 'history', 1460371929, 1460371929, 1),
(21, ' 2013北京高考语文试卷', '', 'paper/f3d242f126601079de9c6a636450a747.xml', 'answer/96f4afe9a2da7d9b05293b66f7cdeb74.xml', 'administrator', 'chinese', 1460966819, 1460966819, 1),
(22, ' 2011北京高考数学（文）试卷', ' 2011北京高考数学（文）试卷', 'paper/94dc81932b689e9e465a3dd1a69d31e6.XML', 'answer/c1fe94632f5e1b4c5b40cb3213d77180.xml', 'administrator', 'math', 1460975962, 1460975962, 1),
(23, ' 2014', '', 'paper/34adc20440a7637f7ca9e86c0bae8df9.xml', '', 'administrator', 'chinese', 1460983656, 1460984378, 0),
(24, ' ', '', 'paper/1040275c50c174eb7bc1d222cf541ba0.xml', '', 'administrator', 'chinese', 1460984118, 1460984376, 0),
(25, ' 11', '11', 'paper/6e6acc6d243e052c3ac7bf9cbc1099e9.xml', 'answer/bd1439caee970f1e86b2cf1a42c1fe89.xml', 'administrator', 'chinese', 1460985321, 1460986379, 0),
(26, ' 2015北京高考卷数学', ' 2015北京高考卷数学', 'paper/867505bbcc22be94d92aeda38e5d47ee.xml', 'answer/1c0984ad88e446495646a664c93b5637.xml', 'administrator', 'math', 1461072667, 1461072667, 1);

-- --------------------------------------------------------

--
-- 表的结构 `resources`
--

CREATE TABLE IF NOT EXISTS `resources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `publisher` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `resources`
--

INSERT INTO `resources` (`id`, `title`, `content`, `publisher`, `active`, `created_at`, `updated_at`) VALUES
(1, 'res1', 'res/f4bb1be3ae4506be9db78daee423047f.xml', 'administrator', 1, 1461111133, 1461111133);

-- --------------------------------------------------------

--
-- 表的结构 `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- 转存表中的数据 `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '考生', 1458007305, 1458007305),
(2, '阅卷人', 1458007342, 1458007342),
(3, '管理员', 1458007367, 1458007367),
(4, 'geography', 1459065280, 1459065280),
(5, 'history', 1459065337, 1459065337),
(6, 'chinese', 1459065361, 1459065361),
(7, 'math', 1459065385, 1459065385);

-- --------------------------------------------------------

--
-- 表的结构 `scorer_paper`
--

CREATE TABLE IF NOT EXISTS `scorer_paper` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_paper_id` int(11) NOT NULL,
  `grade` double(8,2) NOT NULL,
  `detail_xml` longtext COLLATE utf8_unicode_ci NOT NULL,
  `count` int(11) NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `subject_grade` double(8,2) NOT NULL,
  `object_grade` double(8,2) NOT NULL,
  `submit` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=46 ;

--
-- 转存表中的数据 `scorer_paper`
--

INSERT INTO `scorer_paper` (`id`, `user_id`, `user_paper_id`, `grade`, `detail_xml`, `count`, `comment`, `subject_grade`, `object_grade`, `submit`, `created_at`, `updated_at`) VALUES
(19, 2, 4, 87.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer><question id="test01_34_01"><text>4</text></question><question id="test01_34_02_01"><text>1.0</text></question><question id="test01_34_02_02"><text/></question><question id="test01_34_03"><text/></question><question id="select"><text>66</text></question><question id="test01_34_04"><text/></question><question id="test01_35_01"><text/></question><question id="test01_35_02"><text/></question><question id="test01_35_03"><text>1</text></question><question id="test01_36_01"><text>2</text></question><question id="test01_36_03_01"><text>1</text></question><question id="test01_36_04"><text>4</text></question><question id="test01_37_01_01"><text/></question><question id="test01_37_02"><text>2</text></question><question id="test01_36_02"><text>2</text></question><question id="test01_36_03_02"><text>2</text></question><question id="test01_37_01_02"><text>2</text></question></paperanswer>\n', 0, '', 0.00, 0.00, 1, 1458227792, 1458286259),
(20, 2, 5, 74.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer><question id="select"><text>74</text></question></paperanswer>\n', 0, '', 0.00, 0.00, 1, 1458457772, 1458457975),
(23, 2, 6, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer/>\n', 0, '', 0.00, 0.00, 0, 1458460239, 1458460239),
(24, 5, 5, 134.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer><question id="select"><text>74</text></question><question id="test02_41_01"><text>2</text></question><question id="test02_41_02_01"><text>3</text></question><question id="test02_41_02_02"><text>2</text></question><question id="test02_41_03"><text>3</text></question><question id="test02_41_04"><text>4</text></question><question id="test02_42_01"><text>7</text></question><question id="test02_42_02"><text>5</text></question><question id="test02_43_01"><text>10</text></question><question id="test02_43_02"><text>6</text></question><question id="test02_43_03"><text>8</text></question><question id="test02_44_01"><text>3</text></question><question id="test02_44_02"><text>3</text></question><question id="test02_44_03"><text>4</text></question></paperanswer>\n', 0, '', 0.00, 0.00, 1, 1458460987, 1458461035),
(25, 2, 8, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer/>\n', 0, '', 0.00, 0.00, 0, 1458484494, 1458484494),
(27, 2, 9, 68.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer><question id="test01_34_01"><text>0</text></question><question id="test01_34_02_01"><text>0</text></question><question id="test01_34_02_02"><text>0</text></question><question id="select"><text>66</text></question><question id="test01_34_03"><text/></question><question id="test01_34_04"><text/></question><question id="test01_35_01"><text/></question><question id="test01_35_02"><text/></question><question id="test01_35_03"><text/></question><question id="test01_36_01"><text/></question><question id="test01_36_02"><text/></question><question id="test01_36_03_01"><text/></question><question id="test01_36_03_02"><text/></question><question id="test01_36_04"><text/></question><question id="test01_37_01_01"><text/></question><question id="test01_37_01_02"><text/></question><question id="test01_37_02"><text>2</text></question></paperanswer>\n', 17, '', 2.00, 66.00, 1, 1459081945, 1459083337),
(28, 2, 11, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer><question id="select"><text>60</text></question></paperanswer>\n', 17, '', 0.00, 60.00, 0, 1459085080, 1459085098),
(29, 2, 13, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer><question id="select"><text>60</text></question><question id="test01_34_01"><text/></question><question id="test01_34_02_01"><text/></question><question id="test01_37_01_01"><text/></question><question id="test01_37_01_02"><text/></question><question id="test01_37_02"><text/></question><question id="test01_36_04"><text/></question><question id="test01_36_03_02"><text/></question><question id="test01_36_03_01"><text/></question><question id="test01_34_03"><text>3</text></question></paperanswer>\n', 17, '', 0.00, 60.00, 0, 1459155202, 1460080103),
(30, 2, 12, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer/>\n', 17, '该生成绩优异', 0.00, 60.00, 0, 1459162754, 1459171658),
(31, 2, 15, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer/>\n', 6, '', 0.00, 64.00, 0, 1459163123, 1459163123),
(32, 2, 16, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer/>\n', 7, '', 0.00, 64.00, 0, 1460035695, 1460035695),
(33, 2, 17, 2.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer><question id="select"><text>--</text></question><question id="test01_34_01"><text>2</text></question><question id="test01_34_02_01"><text/></question><question id="test01_34_02_02"><text/></question><question id="test01_34_03"><text/></question><question id="test01_34_04"><text/></question><question id="test01_35_01"><text/></question><question id="test01_35_02"><text/></question><question id="test01_35_03"><text/></question><question id="test01_36_01"><text/></question><question id="test01_36_02"><text/></question><question id="test01_36_03_02"><text/></question><question id="test01_36_03_01"><text/></question><question id="test01_36_04"><text/></question><question id="test01_37_01_01"><text/></question><question id="test01_37_01_02"><text/></question><question id="test01_37_02"><text/></question></paperanswer>\n', 17, '', 2.00, 0.00, 1, 1460364434, 1460364502),
(34, 2, 18, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer><question id="test01_15_01"><text/></question></paperanswer>\n', 5, '', 0.00, 0.00, 0, 1460364963, 1460371865),
(35, 2, 19, 56.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer><question id="select"><text>20</text></question><question id="test02_49_01"><text>3</text></question><question id="test02_49_02"><text>2</text></question><question id="test02_50_01_01"><text>4</text></question><question id="test02_50_01_02"><text>8</text></question><question id="test02_50_02"><text>3</text></question><question id="test02_51_01"><text>6</text></question><question id="test02_51_02_01"><text>4</text></question><question id="test02_51_02_02"><text>6</text></question></paperanswer>\n', 9, '杨泓恺批阅', 36.00, 20.00, 1, 1460372007, 1460372170),
(36, 5, 19, 53.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer><question id="test02_51_02_02"><text>6</text></question><question id="test02_51_02_01"><text>4</text></question><question id="test02_51_01"><text>6</text></question><question id="test02_50_02"><text>6</text></question><question id="test02_50_01_02"><text>8</text></question><question id="test02_50_01_01"><text>4</text></question><question id="test02_49_02"><text>12</text></question><question id="test02_49_01"><text>6</text></question><question id="select"><text>1</text></question></paperanswer>\n', 9, 'teacher1教师批改', 52.00, 1.00, 1, 1460373884, 1460373970),
(37, 5, 20, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer><question id="select"><text>15</text></question><question id="2013BeijingGaokao_07"><text>2</text></question></paperanswer>\n', 6, '', 0.00, 15.00, 0, 1460967032, 1460982014),
(38, 5, 21, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer><question id="select"><text>40</text></question></paperanswer>\n', 13, '', 0.00, 40.00, 0, 1460976010, 1460982029),
(39, 2, 21, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer><question id="2011BeijingGaokao_11"><text>2</text></question><question id="2011BeijingGaokao_17_02"><text>8</text></question></paperanswer>\n', 21, '', 0.00, 40.00, 0, 1460986703, 1461034263),
(40, 2, 20, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer/>\n', 23, '', 0.00, 15.00, 0, 1461034203, 1461034203),
(41, 2, 22, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer/>\n', 9, '', 0.00, 0.00, 0, 1461044240, 1461044240),
(42, 11, 22, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer/>\n', 9, '', 0.00, 0.00, 0, 1461115174, 1461115174),
(43, 11, 21, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer/>\n', 21, '', 0.00, 40.00, 0, 1461115180, 1461115180),
(44, 11, 19, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer/>\n', 9, '', 0.00, 0.00, 0, 1461115220, 1461115220),
(45, 11, 20, 0.00, '<?xml version="1.0" encoding="UTF-8"?>\n<paperanswer/>\n', 23, '', 0.00, 15.00, 0, 1461115223, 1461115223);

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_name_unique` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `password`, `department`, `created_at`, `updated_at`) VALUES
(1, 1, 'student', '123456', NULL, 1458007567, 1458007567),
(2, 2, 'teacher', '123456', NULL, 1458007601, 1458007601),
(3, 3, 'administrator', '123456', '北京语言大学', 1458007647, 1458484208),
(4, 3, 'yhk', '123456', NULL, 1458460875, 1458460875),
(6, 4, 'stu_geo', '123456', NULL, 1459065553, 1459065553),
(7, 5, 'stu_his', '123456', NULL, 1459065565, 1459065565),
(8, 6, 'stu_chi', '123456', NULL, 1459065577, 1459065577),
(9, 7, 'stu_mat', '123456', NULL, 1459065603, 1459065603),
(10, 2, 'test1', '123456', NULL, 1461114142, 1461114142),
(11, 2, 'teacher1', '123456', NULL, 1461115157, 1461115157);

-- --------------------------------------------------------

--
-- 表的结构 `user_paper`
--

CREATE TABLE IF NOT EXISTS `user_paper` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `paper_id` int(11) NOT NULL,
  `userAnswer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=23 ;

--
-- 转存表中的数据 `user_paper`
--

INSERT INTO `user_paper` (`id`, `user_id`, `paper_id`, `userAnswer`, `status`, `time`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'user/32ba35c605c19013c6d09f5fff85cc18.xml', '', '', 0, 0),
(2, 1, 1, 'user/e48e0dc3f82565b195b498f5aa24ab64.xml', '', '', 0, 0),
(3, 1, 3, 'user/1d2f5365d4fadd69386f9e7e9cbdf865.xml', '', '', 0, 0),
(4, 1, 3, 'user/1dc2d9e5a656f9e48ade47cd42fddfa5.xml', '', '', 1458134133, 1458134133),
(5, 1, 4, 'user/62c345cb3b545a457f87b7299de7f928.xml', '', '', 1458457468, 1458457468),
(6, 1, 5, 'user/2d477b7caca53ef9eb2bb13d0a740097.xml', '', '', 1458458536, 1458458536),
(7, 1, 5, 'user/a9269e00544bbc9a1b81c2a59ba71cc1.xml', '', '', 1458458986, 1458458986),
(8, 1, 6, 'user/ca189dab9bd7c0711c2b493502738467.xml', '', '', 1458484469, 1458484469),
(9, 6, 7, 'user/657c9da16645cae73ad501728c70ebda.xml', '', '', 1459078787, 1459078787),
(10, 6, 7, 'user/32a7636be2994f232a521a139019ebdc.xml', '', '', 1459079015, 1459079015),
(11, 6, 7, 'user/8788fae7a034d388d392751d3e4c7723.xml', '评阅中', '', 1459085038, 1459085098),
(12, 6, 7, 'user/2dad7a730e5b825ed23e62be3e2f9d94.xml', '未评阅', '', 1459085401, 1459085401),
(13, 6, 7, 'user/8c8851f34db3c8cfec663a8d8378ad67.xml', '评阅中', '2016-03-27 22:48:55', 1459090135, 1459155204),
(14, 7, 16, 'user/362acff549dd20e3f7838a530d9d2299.xml', '未评阅', '2016-03-28 19:00:01', 1459162801, 1459162801),
(15, 6, 17, 'user/ff2f64c04a33e461de6f09e96a8c8dd5.xml', '未评阅', '2016-03-28 19:05:00', 1459163100, 1459163100),
(16, 6, 17, 'user/8ed777abfa7ab352b1995a4a24198fdf.xml', '未评阅', '2016-04-07 21:27:35', 1460035655, 1460035655),
(17, 6, 18, 'user/bc1e0a99359c04c4e63a8b0050f8988e.xml', '已评阅', '2016-04-11 15:42:02', 1460360522, 1460364502),
(18, 7, 19, 'user/0fe5d317cc8dd473a69d8b84b540ea63.xml', '评阅中', '2016-04-11 16:51:03', 1460364663, 1460371865),
(19, 7, 20, 'user/c2181e6ee27623d1be8e7750e96bb7cf.xml', '已评阅', '2016-04-11 18:52:45', 1460371965, 1460373970),
(20, 8, 21, 'user/c684139e2dfdec55bfe6b6d4745055c4.xml', '评阅中', '2016-04-18 16:07:39', 1460966859, 1460978713),
(21, 9, 22, 'user/b090c9715457fd034adcb5309e19dca7.xml', '评阅中', '2016-04-18 18:39:50', 1460975990, 1460982029),
(22, 7, 20, 'user/af280959e11de5aaab58cc6fb3fc19cd.xml', '未评阅', '2016-04-18 21:28:04', 1460986084, 1460986084);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
