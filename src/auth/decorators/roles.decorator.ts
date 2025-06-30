import { applyDecorators, SetMetadata } from '@nestjs/common';
import { ApiExtension } from '@nestjs/swagger';

export const ROLES_KEY = 'roles';

export const Roles = (...roles: string[]) =>
	applyDecorators(
		SetMetadata(ROLES_KEY, roles),
		ApiExtension(`x-${ROLES_KEY}`, `[${roles.join(', ')}]`),
	);
