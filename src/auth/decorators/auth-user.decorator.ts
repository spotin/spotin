import { User } from '@/users/types/user';
import { createParamDecorator, ExecutionContext } from '@nestjs/common';
import { Request } from 'express';

export const AuthUser = createParamDecorator(
	(_: string, ctx: ExecutionContext): User | undefined => {
		const request: Request = ctx.switchToHttp().getRequest();

		return request.user as User | undefined;
	},
);
