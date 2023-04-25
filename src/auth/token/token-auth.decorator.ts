/* eslint-disable @typescript-eslint/ban-types */
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import { ApiSecurity, ApiUnauthorizedResponse } from '@nestjs/swagger';
import { TokenAuthGuard } from './token-auth.guard';

export const TokenAuth = (
  ...guards: (Function | CanActivate)[]
): (<TFunction extends Function, Y>(
  target: object | TFunction,
  propertyKey?: string | symbol | undefined,
  descriptor?: TypedPropertyDescriptor<Y> | undefined,
) => void) =>
  applyDecorators(
    UseGuards(TokenAuthGuard, ...guards),
    ApiSecurity('token'),
    ApiUnauthorizedResponse({
      description: 'Wrong token.',
    }),
  );
