import { createParamDecorator, ExecutionContext } from '@nestjs/common';
import { User } from '@prisma/client';
import { Request } from 'express';

export const AuthUser = createParamDecorator(
  (data: string, ctx: ExecutionContext): User => {
    const request: Request = ctx.switchToHttp().getRequest();

    return request.user as User;
  },
);
