# noinspection SqlNoDataSourceInspectionForFile

/* USE `laravel_glazy`; */

/******************* CLEAN UP OLD DB***********************/

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE `laravel_glazy`.`material_collections`;
DROP TABLE `laravel_glazy`.`material_collection_types`;
DROP TABLE `laravel_glazy`.`material_material_collections`;

DELETE FROM `laravel_glazy`.`recipe_materials`
WHERE `laravel_glazy`.`recipe_materials`.`deleted_at` IS NOT NULL;

DELETE FROM `laravel_glazy`.`recipe_materials`
WHERE `laravel_glazy`.`recipe_materials`.`material_id`
			NOT IN (select id from `laravel_glazy`.`materials`);

DELETE FROM `laravel_glazy`.`recipe_atmospheres`
WHERE `laravel_glazy`.`recipe_atmospheres`.`recipe_id`
			NOT IN (select id from `laravel_glazy`.`recipes`);

DELETE FROM `laravel_glazy`.`recipe_collections`
WHERE `laravel_glazy`.`recipe_collections`.`recipe_id`
			NOT IN (select id from `laravel_glazy`.`recipes`);

DELETE FROM `laravel_glazy`.`materials`
WHERE `laravel_glazy`.`materials`.`deleted_at` IS NOT NULL;

DELETE FROM `laravel_glazy`.`recipes`
WHERE `laravel_glazy`.`recipes`.`deleted_at` IS NOT NULL;

SET FOREIGN_KEY_CHECKS=1;


/******************* USERS ***********************/
INSERT INTO `glazyc`.`users`(
`id`,
`email`,
`name`,
`first_name`,
`last_name`,
`password`,
`confirmed`,
`last_login`,
`created_at`,
`updated_at`,
`deleted_at`
)

SELECT
`id`,
`email`,
CONCAT(`first_name`, ' ', `last_name`),
`first_name`,
`last_name`,
`password`,
1,
`last_login`,
`created_at`,
`updated_at`,
`deleted_at`
FROM `laravel_glazy`.`users`

WHERE `laravel_glazy`.`users`.`id` > 3;

/***
DELETE unneeded old users id's = 4, 5, 6....
 */
/******************* ROLE_USER ***********************/
INSERT INTO `glazyc`.`role_user`(
`user_id`,
`role_id`
)

SELECT
`id`,
3
FROM `laravel_glazy`.`users`

WHERE `laravel_glazy`.`users`.`id` > 6;



/******************* USER_PROFILES ***********************/
INSERT INTO `glazyc`.`user_profiles`(
`id`,
`user_id`,
`title`,
`description`,
`location`,
`image_filename`,
`url`,
`pinterest`,
`facebook`,
`instagram`,
`created_at`,
`updated_at`,
`deleted_at`
)

SELECT
`id`,
`user_id`,
`title`,
`description`,
`location`,
`image_filename`,
`url`,
`pinterest`,
`facebook`,
`instagram`,
`created_at`,
`updated_at`,
`deleted_at`
FROM `laravel_glazy`.`user_profiles`

WHERE `laravel_glazy`.`user_profiles`.`user_id` > 6;


/******************* MATERIALS (PRIMITIVE) ***********************/
SET FOREIGN_KEY_CHECKS=0;

INSERT INTO `glazyc`.`materials`(
`id`,
`old_id`,
`parent_id`,
`name`,
`other_names`,
`insight_name`,
`description`,
`material_type_id`,
`is_primitive`,
`is_theoretical`,
`created_by_user_id`,
`updated_by_user_id`,
`created_at`,
`updated_at`,
`deleted_at`
)

SELECT
`id` + 15000,
`id`,
`parent_id` + 15000,
`short_name`,
`name`,
`insight_name`,
`description`,
`material_type_id`,
TRUE,
`is_theoretical`,
`created_by_user_id`,
`updated_by_user_id`,
`created_at`,
`updated_at`,
`deleted_at`
FROM `laravel_glazy`.`materials`;

UPDATE `glazyc`.`materials`
  INNER JOIN `glazyc`.`material_types`
    ON `glazyc`.`materials`.`material_type_id` = `glazyc`.`material_types`.`old_material_type_id`
SET `glazyc`.`materials`.`material_type_id` = 	`glazyc`.`material_types`.`id`;

SET FOREIGN_KEY_CHECKS=1;



/******************* MATERIALS (PRIMITIVE) ANALYSIS ***********************/

INSERT INTO `glazyc`.`material_analyses`(
`material_id`,
`SiO2_percent`,
`Al2O3_percent`,
`B2O3_percent`,
`Li2O_percent`,
`K2O_percent`,
`Na2O_percent`,
`KNaO_percent`,
`MgO_percent`,
`CaO_percent`,
`SrO_percent`,
`BaO_percent`,
`ZnO_percent`,
`PbO_percent`,
`P2O5_percent`,
`F_percent`,
`V2O5_percent`,
`Cr2O3_percent`,
`MnO_percent`,
`MnO2_percent`,
`FeO_percent`,
`Fe2O3_percent`,
`CoO_percent`,
`NiO_percent`,
`CuO_percent`,
`Cu2O_percent`,
`TiO2_percent`,
`ZrO_percent`,
`ZrO2_percent`,
`SnO2_percent`,
`SiO2_umf`,
`Al2O3_umf`,
`B2O3_umf`,
`Li2O_umf`,
`K2O_umf`,
`Na2O_umf`,
`KNaO_umf`,
`MgO_umf`,
`CaO_umf`,
`SrO_umf`,
`BaO_umf`,
`ZnO_umf`,
`PbO_umf`,
`P2O5_umf`,
`F_umf`,
`V2O5_umf`,
`Cr2O3_umf`,
`MnO_umf`,
`MnO2_umf`,
`FeO_umf`,
`Fe2O3_umf`,
`CoO_umf`,
`NiO_umf`,
`CuO_umf`,
`Cu2O_umf`,
`TiO2_umf`,
`ZrO_umf`,
`ZrO2_umf`,
`SnO2_umf`,
`weight`,
`formula_weight`,
`loi`,
`thermal_expansion`,
`created_at`,
`updated_at`,
`deleted_at`
)

SELECT
`id` + 15000,
`SiO2_percent`,
`Al2O3_percent`,
`B2O3_percent`,
`Li2O_percent`,
`K2O_percent`,
`Na2O_percent`,
`K2O_percent` + `Na2O_percent`,
`MgO_percent`,
`CaO_percent`,
`SrO_percent`,
`BaO_percent`,
`ZnO_percent`,
`PbO_percent`,
`P2O5_percent`,
`F_percent`,
`V2O5_percent`,
`Cr2O3_percent`,
`MnO_percent`,
`MnO2_percent`,
`FeO_percent`,
`Fe2O3_percent`,
`CoO_percent`,
`NiO_percent`,
`CuO_percent`,
`Cu2O_percent`,
`TiO2_percent`,
`ZrO_percent`,
`ZrO2_percent`,
`SnO2_percent`,
`SiO2`,
`Al2O3`,
`B2O3`,
`Li2O`,
`K2O`,
`Na2O`,
`K2O` + `Na2O`,
`MgO`,
`CaO`,
`SrO`,
`BaO`,
`ZnO`,
`PbO`,
`P2O5`,
`F`,
`V2O5`,
`Cr2O3`,
`MnO`,
`MnO2`,
`FeO`,
`Fe2O3`,
`CoO`,
`NiO`,
`CuO`,
`Cu2O`,
`TiO2`,
`ZrO`,
`ZrO2`,
`SnO2`,
`weight`,
`formula_weight`,
`loi`,
`thermal_expansion`,
`created_at`,
`updated_at`,
`deleted_at`
FROM `laravel_glazy`.`materials`;





/******************* MATERIALS (PRIMITIVE) HAZARDS ***********************/

INSERT INTO `glazyc`.`material_hazards`(
`material_id`,
`hmis_health`,
`hmis_flammability`,
`hmis_physical_hazard`,
`hmis_personal_protection`,
`ghs`,
`ghs_signal_word`,
`ghs_statements`,
`sds_url`,
`created_at`,
`updated_at`,
`deleted_at`
)

SELECT
`id` + 15000,
`hmis_health`,
`hmis_flammability`,
`hmis_physical_hazard`,
`hmis_personal_protection`,
`ghs`,
`ghs_signal_word`,
`ghs_statements`,
`sds_url`,
`created_at`,
`updated_at`,
`deleted_at`
FROM `laravel_glazy`.`materials`;



/******************* MATERIALS (COMPOSITE) ***********************/
SET FOREIGN_KEY_CHECKS=0;

INSERT INTO `glazyc`.`materials`(
`id`,
`name`,
`description`,
`material_type_id`,
`is_analysis`,
`from_orton_cone_id`,
`to_orton_cone_id`,
`surface_type_id`,
`transparency_type_id`,
`color_name`,
`rgb_r`,
`rgb_g`,
`rgb_b`,
`rating_total`,
`rating_number`,
`rating_average`,
`thumbnail_id`,
`base_composite_hash`,
`additive_composite_hash`,
`is_private`,
`created_by_user_id`,
`updated_by_user_id`,
`created_at`,
`updated_at`,
`deleted_at`
)
SELECT
`id`,
`name`,
`description`,
`recipe_type_id`,
`is_analysis`,
`from_orton_cone_id`,
`to_orton_cone_id`,
`surface_type_id`,
`transparency_type_id`,
`color_name`,
`rgb_r`,
`rgb_g`,
`rgb_b`,
`rating_total`,
`rating_number`,
`rating_total`/`rating_number`,
`thumbnail_id`,
`materials_hash`,
`additive_materials_hash`,
`is_private`,
`created_by_user_id`,
`updated_by_user_id`,
`created_at`,
`updated_at`,
`deleted_at`
FROM `laravel_glazy`.`recipes`;


/******************* UPDATE MATERIAL TYPES FOR COMPOSITE ***********************/

UPDATE `glazyc`.`materials`
	INNER JOIN `glazyc`.`material_types`
		ON `glazyc`.`materials`.`material_type_id` = `glazyc`.`material_types`.`old_recipe_type_id`
SET `glazyc`.`materials`.`material_type_id` = 	`glazyc`.`material_types`.`id`
WHERE `glazyc`.`materials`.`is_primitive` IS FALSE;

SET FOREIGN_KEY_CHECKS=1;

/******************* UPDATE MATERIAL NAMES ***********************/

UPDATE `glazyc`.`materials`
SET `glazyc`.`materials`.`name` = 	`glazyc`.`materials`.`other_names`,
	`glazyc`.`materials`.`other_names` = NULL
WHERE `glazyc`.`materials`.`name` IS NULL OR `glazyc`.`materials`.`name` = '';;




/******************* MATERIALS (COMPOSITE) ANALYSIS ***********************/

INSERT INTO `glazyc`.`material_analyses`(
`material_id`,
`SiO2_percent`,
`Al2O3_percent`,
`B2O3_percent`,
`Li2O_percent`,
`K2O_percent`,
`Na2O_percent`,
`KNaO_percent`,
`MgO_percent`,
`CaO_percent`,
`SrO_percent`,
`BaO_percent`,
`ZnO_percent`,
`PbO_percent`,
`P2O5_percent`,
`F_percent`,
`V2O5_percent`,
`Cr2O3_percent`,
`MnO_percent`,
`MnO2_percent`,
`FeO_percent`,
`Fe2O3_percent`,
`CoO_percent`,
`NiO_percent`,
`CuO_percent`,
`Cu2O_percent`,
`TiO2_percent`,
`ZrO_percent`,
`ZrO2_percent`,
`SnO2_percent`,
`SiO2_umf`,
`Al2O3_umf`,
`B2O3_umf`,
`Li2O_umf`,
`K2O_umf`,
`Na2O_umf`,
`KNaO_umf`,
`MgO_umf`,
`CaO_umf`,
`SrO_umf`,
`BaO_umf`,
`ZnO_umf`,
`PbO_umf`,
`P2O5_umf`,
`F_umf`,
`V2O5_umf`,
`Cr2O3_umf`,
`MnO_umf`,
`MnO2_umf`,
`FeO_umf`,
`Fe2O3_umf`,
`CoO_umf`,
`NiO_umf`,
`CuO_umf`,
`Cu2O_umf`,
`TiO2_umf`,
`ZrO_umf`,
`ZrO2_umf`,
`SnO2_umf`,
`R2O_umf`,
`RO_umf`,
`SiO2_Al2O3_ratio_umf`,
`SiO2_Al2O3`,
`created_at`,
`updated_at`,
`deleted_at`
)

SELECT
`id`,
`SiO2_percent`,
`Al2O3_percent`,
`B2O3_percent`,
`Li2O_percent`,
`K2O_percent`,
`Na2O_percent`,
`K2O_percent` + `Na2O_percent`,
`MgO_percent`,
`CaO_percent`,
`SrO_percent`,
`BaO_percent`,
`ZnO_percent`,
`PbO_percent`,
`P2O5_percent`,
`F_percent`,
`V2O5_percent`,
`Cr2O3_percent`,
`MnO_percent`,
`MnO2_percent`,
`FeO_percent`,
`Fe2O3_percent`,
`CoO_percent`,
`NiO_percent`,
`CuO_percent`,
`Cu2O_percent`,
`TiO2_percent`,
`ZrO_percent`,
`ZrO2_percent`,
`SnO2_percent`,
`ror2o_unity_SiO2`,
`ror2o_unity_Al2O3`,
`ror2o_unity_B2O3`,
`ror2o_unity_Li2O`,
`ror2o_unity_K2O`,
`ror2o_unity_Na2O`,
`ror2o_unity_K2O` + `ror2o_unity_Na2O`,
`ror2o_unity_MgO`,
`ror2o_unity_CaO`,
`ror2o_unity_SrO`,
`ror2o_unity_BaO`,
`ror2o_unity_ZnO`,
`ror2o_unity_PbO`,
`ror2o_unity_P2O5`,
`ror2o_unity_F`,
`ror2o_unity_V2O5`,
`ror2o_unity_Cr2O3`,
`ror2o_unity_MnO`,
`ror2o_unity_MnO2`,
`ror2o_unity_FeO`,
`ror2o_unity_Fe2O3`,
`ror2o_unity_CoO`,
`ror2o_unity_NiO`,
`ror2o_unity_CuO`,
`ror2o_unity_Cu2O`,
`ror2o_unity_TiO2`,
`ror2o_unity_ZrO`,
`ror2o_unity_ZrO2`,
`ror2o_unity_SnO2`,
`ror2o_unity_r2o`,
`ror2o_unity_ro`,
`SiO2_Al2O3_ratio`,
GeometryFromText( CONCAT( 'POINT(', `ror2o_unity_SiO2`, ' ', `ror2o_unity_Al2O3`, ')' ) ),
`created_at`,
`updated_at`,
`deleted_at`
FROM `laravel_glazy`.`recipes`;

/******************* MATERIALS MATERIALS ***********************/

INSERT INTO `glazyc`.`material_materials`(
	`parent_material_id`,
	`component_material_id`,
	`percentage_amount`,
	`is_additional`,
	`created_at`,
	`updated_at`
)

SELECT
	`recipe_id`,
	`material_id` + 15000,
	`percentage_amount`,
	`is_additional`,
	`created_at`,
	`updated_at`
FROM `laravel_glazy`.`recipe_materials`;


/******************* MATERIAL ATMOSPHERES ***********************/

INSERT INTO `glazyc`.`material_atmospheres`(
  `material_id`,
  `atmosphere_id`,
  `created_at`,
  `updated_at`
)
SELECT
  `recipe_id`,
  `atmosphere_id`,
  `created_at`,
  `updated_at`
FROM `laravel_glazy`.`recipe_atmospheres`;


/******************* MATERIAL IMAGES ***********************/

INSERT INTO `glazyc`.`material_images`(
	`id`,
	`material_id`,
	`title`,
	`description`,
	`dominant_rgb_r`,
	`dominant_rgb_g`,
	`dominant_rgb_b`,
	`secondary_rgb_r`,
	`secondary_rgb_g`,
	`secondary_rgb_b`,
	`filename`,
	`created_by_user_id`,
	`updated_by_user_id`,
	`created_at`,
	`updated_at`
)
SELECT
	`id`,
	`recipe_id`,
	`title`,
	`description`,
	`dominant_rgb_r`,
	`dominant_rgb_g`,
	`dominant_rgb_b`,
	`secondary_rgb_r`,
	`secondary_rgb_g`,
	`secondary_rgb_b`,
	`filename`,
	`created_by_user_id`,
	`updated_by_user_id`,
	`created_at`,
	`updated_at`
FROM `laravel_glazy`.`recipe_images`;


/******************* COLLECTIONS ***********************/

INSERT INTO `glazyc`.`collections`(
  `id`,
  `collection_type_id`,
  `name`,
  `description`,
  `material_count`,
  `image_filename`,
  `thumbnail_id`,
  `is_private`,
  `created_by_user_id`,
  `updated_by_user_id`,
  `created_at`,
  `updated_at`
)
SELECT
  `id`,
  `collection_type_id`,
  `name`,
  `description`,
  `recipe_count`,
  `image_filename`,
  `thumbnail_1_id`,
  `is_private`,
  `created_by_user_id`,
  `updated_by_user_id`,
  `created_at`,
  `updated_at`
FROM `laravel_glazy`.`collections`;


/******************* COLLECTION MATERIALS ***********************/

INSERT INTO `glazyc`.`collection_materials`(
	`collection_id`,
	`material_id`,
	`created_at`,
	`updated_at`
)
	SELECT
		`collection_id`,
		`recipe_id`,
		`created_at`,
		`updated_at`
	FROM `laravel_glazy`.`recipe_collections`;


/******************* MATERIAL REVIEWS ***********************/

INSERT INTO `glazyc`.`material_reviews`(
	`material_id`,
	`user_id`,
	`title`,
	`description`,
	`rating`,
	`created_at`,
	`updated_at`
)
	SELECT
		`recipe_id`,
		`user_id`,
		`title`,
		`description`,
		`rating`,
		`created_at`,
		`updated_at`
	FROM `laravel_glazy`.`recipe_reviews`;







