import { User } from '@/users/types/user';
import { createParamDecorator, ExecutionContext } from '@nestjs/common';
import { Request } from 'express';

export const AuthUser = createParamDecorator(
	(_: string, ctx: ExecutionContext): User | undefined => {
		const request: Request = ctx.switchToHttp().getRequest();

		const user = request.user;

		if (typeof user === 'object') {
			return user as User;
		}

		return undefined;
	},
);
