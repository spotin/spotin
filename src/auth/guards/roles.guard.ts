import { ROLES_KEY } from '@/auth/decorators/roles.decorator';
import { User } from '@/users/types/user';
import { Injectable, CanActivate, ExecutionContext } from '@nestjs/common';
import { Reflector } from '@nestjs/core';
import { Request } from 'express';

@Injectable()
export class RolesGuard implements CanActivate {
	constructor(private reflector: Reflector) {}

	canActivate(context: ExecutionContext): boolean {
		const roles = this.reflector.getAllAndOverride<string[]>(ROLES_KEY, [
			context.getHandler(),
			context.getClass(),
		]);

		if (!roles) {
			return true;
		}

		const request: Request = context.switchToHttp().getRequest();

		const user = request.user as User;

		return roles.includes(user.role);
	}
}
