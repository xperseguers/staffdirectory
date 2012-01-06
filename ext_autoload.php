<?php
$extensionClassesPath = t3lib_extMgm::extPath('staffdirectory') . 'Classes/';
return array(
	'tx_staffdirectory_controller_abstractcontroller' => $extensionClassesPath . 'Controller/AbstractController.php',
	'tx_staffdirectory_persistence_dao' => $extensionClassesPath . 'Persistence/Dao.php',
	'tx_staffdirectory_domain_model_abstractentity' => $extensionClassesPath . 'Domain/Model/AbstractEntity.php',
	'tx_staffdirectory_domain_model_department' => $extensionClassesPath . 'Domain/Model/Department.php',
	'tx_staffdirectory_domain_model_member' => $extensionClassesPath . 'Domain/Model/Member.php',
	'tx_staffdirectory_domain_model_staff' => $extensionClassesPath . 'Domain/Model/Staff.php',
	'tx_staffdirectory_domain_repository_abstractrepository' => $extensionClassesPath . 'Domain/Repository/AbstractRepository.php',
	'tx_staffdirectory_domain_repository_departmentrepository' => $extensionClassesPath . 'Domain/Repository/DepartmentRepository.php',
	'tx_staffdirectory_domain_repository_factory' => $extensionClassesPath . 'Domain/Repository/Factory.php',
	'tx_staffdirectory_domain_repository_memberrepository' => $extensionClassesPath . 'Domain/Repository/MemberRepository.php',
	'tx_staffdirectory_domain_repository_staffrepository' => $extensionClassesPath . 'Domain/Repository/StaffRepository.php',
	'tx_staffdirectory_utility_typoscript' => $extensionClassesPath . 'Utility/TypoScript.php',
);
?>