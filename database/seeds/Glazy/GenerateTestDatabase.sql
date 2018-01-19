delete `collection_materials`
from `collection_materials`
inner join `collections`
ON `collection_materials`.`collection_id` = `collections`.`id`
where `collections`.`created_by_user_id` != 1 && `collections`.`created_by_user_id` != 7;

delete from collections where created_by_user_id != 1 && created_by_user_id != 7;

update `collections`
inner join `material_images`
ON `collections`.`thumbnail_id` = `material_images`.`id`
SET `collections`.`thumbnail_id` = NULL
where `material_images`.`created_by_user_id` != 1 && `material_images`.`created_by_user_id` != 7;

update `materials`
inner join `material_images`
ON `materials`.`thumbnail_id` = `material_images`.`id`
SET `materials`.`thumbnail_id` = NULL
where `material_images`.`created_by_user_id` != 1 && `material_images`.`created_by_user_id` != 7;

delete from material_images where created_by_user_id != 1 && created_by_user_id != 7;

delete material_analyses
from material_analyses
inner join materials
on material_analyses.material_id = materials.id
where materials.created_by_user_id != 1 && materials.created_by_user_id != 7;

delete material_materials
from material_materials
inner join materials
on material_materials.component_material_id = materials.id
where materials.created_by_user_id != 1 && materials.created_by_user_id != 7;

delete material_materials
from material_materials
inner join materials
on material_materials.parent_material_id = materials.id
where materials.created_by_user_id != 1 && materials.created_by_user_id != 7;

delete material_atmospheres
from material_atmospheres
inner join materials
on material_atmospheres.material_id = materials.id
where materials.created_by_user_id != 1 && materials.created_by_user_id != 7;

delete material_reviews
from material_reviews
inner join materials
on material_reviews.material_id = materials.id
where materials.created_by_user_id != 1 && materials.created_by_user_id != 7;

delete from material_reviews where user_id != 1 && user_id != 7;

delete collection_materials
from collection_materials
inner join materials
on collection_materials.material_id = materials.id
where materials.created_by_user_id != 1 && materials.created_by_user_id != 7;

delete material_hazards
from material_hazards
inner join materials
on material_hazards.material_id = materials.id
where materials.created_by_user_id != 1 && materials.created_by_user_id != 7;

update `materials`
inner join `materials` as parent_materials
ON `materials`.`parent_id` = `parent_materials`.`id`
SET `materials`.`parent_id` = NULL
where `parent_materials`.`created_by_user_id` != 1 && `parent_materials`.`created_by_user_id` != 7;

delete from materials where created_by_user_id != 1 && created_by_user_id != 7;

delete from user_profiles where user_id != 1 && user_id != 7;

delete from user_materials where user_id != 1 && user_id != 7;

delete from users where id != 1 && id != 7;


delete from collection_materials where material_id < 6000;
delete from material_analyses where material_id < 6000;
delete from material_materials where parent_material_id < 6000;
delete material_atmospheres
from material_atmospheres
inner join materials
on material_atmospheres.material_id = materials.id
where materials.id < 6000;
update `materials`
SET `materials`.`thumbnail_id` = NULL
where materials.id < 6000;
update `collections`
inner join `material_images`
ON `collections`.`thumbnail_id` = `material_images`.`id`
SET `collections`.`thumbnail_id` = NULL
where `material_images`.`material_id` < 6000;
delete material_images
from material_images
inner join materials
on material_images.material_id = materials.id
where materials.id < 6000;
delete from material_reviews where material_id < 6000;

delete from materials where id < 6000;


