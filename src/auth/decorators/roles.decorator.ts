import { applyDecorators, SetMetadata } from '@nestjs/common';
import { ApiExtension } from '@nestjs/swagger';

export const ROLES_KEY = 'roles';

// eslint-disable-next-line @typescript-eslint/explicit-function-return-type
export const Roles = (...roles: string[]) =>
	applyDecorators(
		SetMetadata(ROLES_KEY, roles),
		ApiExtension(`x-${ROLES_KEY}`, `[${roles.join(', ')}]`),
	);
